#!/bin/bash

LOG=/var/log/role-builder.log
SCALR_IMPORT_STRING="%SZR_IMPORT_STRING%"
BEHAVIOURS="%BEHAVIOURS%"
PLATFORM="%PLATFORM%"
DEV="%DEV%"
RECIPES="%RECIPES%"
BUILD_ONLY="%BUILD_ONLY%"

for recipe in $RECIPES; do
	key=`echo $recipe | tr '=' ' ' | awk '{print $1}'`
	value=`echo $recipe | tr '=' ' ' | awk '{print $2}'`
	declare $key=$value
done

CHEF_RUNLIST='{ "scalarizr": { "behaviour": [ "'$(echo $BEHAVIOURS | sed 's/ /\", \"/g')'" ], "platform" : "'$PLATFORM'", "dev" : "'$DEV'"}, "run_list": [ '

get_behaviour() {
	bhv="$1"
	if [ -n "${!bhv}" ]; then
		echo "\"recipe[${!bhv}]\", "
	elif [ "$bhv" = "app" ]; then
                echo "\"recipe[apache2]\", "
        elif [ "$bhv" = "mysql" ]; then
                echo "\"recipe[mysql::server]\", "
        elif [ "$bhv" = "www" ]; then
                echo "\"recipe[nginx]\", "
        elif [ "$bhv" = "memcached" ]; then
                echo "\"recipe[memcached]\", "
        fi	
}

for bh in $BEHAVIOURS; do
	recipe=`get_behaviour $bh`
        CHEF_RUNLIST="$CHEF_RUNLIST $recipe"
done

CHEF_RUNLIST="$CHEF_RUNLIST\"recipe[scalarizr]\" ] }"

exec 2>$LOG

action () {
	if tty >/dev/null 2>&1; then
		_col=$(stty -a | grep columns | awk '{print $7}' | sed 's/;//')
	else
		_col=''
	fi
	echo -ne "$1"
	len=${#1}
	eval $2 >> $LOG 2>&1
	
	if [ "$?" -ne 0 ]; then
		if [ -n "$_col" ]; then
			printf "%$[_col-20-len]s [ Failed ]\r\nSee $LOG fore more info.\r\n"
		else
			printf " [ Failed ]\r\nSee $LOG fore more info.\r\n"
		fi
		exit 1
	else
		if [ -n "$_col" ]; then
			printf "%$[_col-20-len]s [ OK ]\r\n"
		else
			printf " [ OK ]\r\n"
		fi
	fi
	
	echo -e '\r\n\r\n' >> $LOG
}


rhel=$(python -c "import platform; d = platform.dist(); print int(d[0].lower() in ['centos', 'rhel', 'redhat'] and d[1].split('.')[0])")
fedora=$(python -c "import platform; d = platform.dist(); print int((d[0].lower() == 'fedora' or (d[0].lower() == 'redhat' and d[2].lower() == 'werewolf')) and d[1].split('.')[0])")

if [ "$rhel" -eq 0 ] && [ "$fedora" -eq 0 ]; then
	action "Updating package list" "apt-get update"
	action "Installing essential packages" "apt-get -y install ruby ruby1.8-dev libopenssl-ruby rdoc ri irb build-essential wget make tar ssl-cert"			
else
	action "Installing EPEL repository"    "rpm -Uvh --replacepkgs http://download.fedora.redhat.com/pub/epel/5/i386/epel-release-5-4.noarch.rpm"
	action "Installing ELFF repository"    "rpm -Uvh --replacepkgs http://download.elff.bravenet.com/5/i386/elff-release-5-3.noarch.rpm"
	action "Installing essential packages" "yum -y install ruby ruby-shadow ruby-ri ruby-rdoc gcc gcc-c++ ruby-devel ruby-static wget make tar"
fi

cd /tmp

action 'Downloading rubygems' "wget -c http://production.cf.rubygems.org/rubygems/rubygems-1.3.7.tgz"
action 'Unpacking rubygems' "tar zxf rubygems-1.3.7.tgz"
cd rubygems-1.3.7
action "Installing rubygems" "ruby setup.rb --no-format-executable --no-ri --no-rdoc"
action "Installing chef" "gem install chef --no-ri --no-rdoc"
mkdir -p /tmp/chef-solo
action "Creating chef configuration file" "echo -e 'file_cache_path \"/tmp/chef-solo\"\r\ncookbook_path \"/tmp/chef-solo\"' > /etc/solo.rb"
action "Unpacking cookbooks"	"tar zxf /tmp/recipes.tar.gz -C /tmp/chef-solo"
action "Creating runlist" 		'echo $CHEF_RUNLIST | tee /tmp/soft.json'
action "Installing software" "chef-solo -c /etc/solo.rb -j /tmp/soft.json"
if [ "0" = "$BUILD_ONLY" ]; then
	action "Starting importing to Scalr in background" "$SCALR_IMPORT_STRING &"
	echo "Scalarizr's log:"
	tail -f /var/log/scalarizr.log | while read LINE; do
	        [[ "${LINE}" =~ 'Rebundle complete!' ]] && break
	        echo $LINE
	done
fi
echo "Done!"
exit 0
