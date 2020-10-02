This tool is strictly 'as-is'.

To use this tool to inspect your ethos data

1. Rename env.example to .env
2. To the .env file add your api key and region to it (US  = 'com', Canada = 'ca' ...)
   1. The region is just the top level domain for your regions domain.  See https://resources.elluciancloud.com/bundle/ethos_integration_ref_reference/page/r_region_domain_ip_addresses.html
3. Review the examples under /scripts/examples
4. Create a folder to store your scripts.  If you want to get the latest version of this tool be sure to preserve your folder prior to running git clone again
5. Create a .php file for your scripts add the require statememnts:
   ```<?php
            require_once "similarToEthosFunctions.php";
            require_once "helperFunctions.php";
            require_once "workflowFunctions.php";

The ee functions used here a simulations of the actual functions called by ellucian workflow.
The names arguments match however the data returned might not be exactly the same.
