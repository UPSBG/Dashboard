# UPS_Dashboard

1. Installation Manually

	1. Prepare UPS_Dashboard Module
	2. Copy UPS_Dashboard Module to app/code
	3. Enable the Module
		>> php bin/magento module:enable UPS_Dashboard
	4. RUN all commands
		>> php bin/magento setup:upgrade
		>> php bin/magento setup:di:compile
		>> php bin/magento setup:static-content:deploy
		>> php bin/magento cache:flush
		
1. Installation using composer
		
	1. Goto root DIR from command line.
	2. RUN composer require command	
		>> composer require ups/dashboard
		
	3. Enable the Module
		>> php bin/magento module:enable UPS_Dashboard
	4. RUN all commands
		>> php bin/magento setup:upgrade
		>> php bin/magento setup:di:compile
		>> php bin/magento setup:static-content:deploy
		>> php bin/magento cache:flush

#changelog
1. Modify core connect module to global/default and website level wise.

#release
>> Backend configuration scope website/default level wise.
>> Compatible upto magento 2.4.8