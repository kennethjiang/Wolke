<div style="width:640px;padding:10px;border:0px solid red;">
	<div style="float:left;width:300px;border:0px solid green;">
		<div class="role-info-field">
			<div class="role-info-field-name">Name:</div>
			<div class="role-info-field-value">{$dbRole->name|truncate:25:'..':true:true}</div>
		</div>
		<div class="role-info-field">
			<div class="role-info-field-name">Group:</div>
			<div class="role-info-field-value">{$dbRole->groupName}</div>
		</div>
		<div class="role-info-field">
			<div class="role-info-field-name">Behaviors:</div>
			<div class="role-info-field-value">{$dbRole->behaviorsList}</div>
		</div>
		<div class="role-info-field">
			<div class="role-info-field-name">OS:</div>
			<div class="role-info-field-value">{$dbRole->os}</div>
		</div>
		<div class="role-info-field">
			<div class="role-info-field-name">Architecture:</div>
			<div class="role-info-field-value">{$dbRole->architecture}</div>
		</div>
		<div class="role-info-field">
			<div class="role-info-field-name">Scalr agent:</div>
			<div class="role-info-field-value">{if $dbRole->generation == 1}ami-scripts{else}scalarizr{/if}</div>
		</div>
		{if $dbRole->tagsString}
		<div class="role-info-field">
			<div class="role-info-field-name">Tags:</div>
			<div class="role-info-field-value">{$dbRole->tagsString}</div>
		</div>
		{/if}
	</div>
	<div style="float:right;width:320px;border:0px solid red;font-size:12px;margin-bottom: 10px;font-family:'Lucida Grande',verdana,arial,helvetica,sans-serif;">
		<div style="margin-bottom:10px;">
			<div style="padding:2px 0px 2px 0px;">Description:</div>
			<div style="font-size:12px;">
				{if $dbRole->description}{$dbRole->description}{else}<i>Description not available for this role</i>{/if}
			</div>
		</div>
		<div>
			<div style="padding:2px 0px 2px 0px;">Installed software:</div>
			<div style="font-size:12px;">
				{if $dbRole->softwareList}{$dbRole->softwareList}{else}<i>Software list not available for this role</i>{/if}
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
	<div class="role-info-field" style="padding-top:10px;">
		<div>Available on:</div>
		{section name=id loop=$dbRole->platformsList}
		<div style="padding-top:2px;">&bull; {$dbRole->platformsList[id].name} in {$dbRole->platformsList[id].locations}</div>
		{/section}
	</div>
</div>