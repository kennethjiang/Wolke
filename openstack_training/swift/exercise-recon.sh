# copy to /usr/bin
sudo cp swift/bin/swift-recon-cron /usr/bin/

# make recon cache directory
sudo mkdir /var/cache/swift
sudo chown swift:swift /var/cache/swift

# modify object-server.conf
sudo sed -i /etc/swift/object-server.conf -e 's/\[object-replicator\]/\[object-replicator\]\nrecon_enable = yes\nrecon_cache_path = \/var\/cache\/swift/g'

sudo sed -i /etc/swift/object-server.conf -e 's/pipeline = object-server/pipeline = recon object-server/g'

sudo echo "" | sudo tee -a /etc/swift/object-server.conf
sudo echo "[filter:recon]" | sudo tee -a /etc/swift/object-server.conf
sudo echo "use = egg:swift#recon" | sudo tee -a /etc/swift/object-server.conf
sudo echo "recon_cache_path = /var/cache/swift" | sudo tee -a /etc/swift/object-server.conf

# add recon to cron jobs
crontab -l | echo "*/5 * * * * /usr/bin/swift-recon-cron /etc/swift/object-server.conf" | crontab -

# restart swift
sudo swift-init all restart

