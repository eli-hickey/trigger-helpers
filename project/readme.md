This tool is strictly 'as is'.

To use this tool to inspect your ethos data

1. Rename env.example to .env
1. To the .env file add your api key and region to it (US = 'com', Canada = 'ca' ...)
   1. The region is just the top level domain for your regions domain. See https://resources.elluciancloud.com/bundle/ethos_integration_ref_reference/page/r_region_domain_ip_addresses.html
1. Review the examples under /scripts/examples
1. Open the /scripts/examples/ethosExamples.php
1. Add a breakpoint on the last line
1. Run (f5)

You are now ready to get your ethos data with some ee functions.

1. Create a folder to store your scripts. If you want to get the latest version of this tool be sure to preserve your folder prior to running git clone again
1. Copy @generate from /examples to your folder
1. Create a .php file for your scripts add the require statememnts:
   ```<?php
            require_once "helperFunctions.php";
            //triggerStart
               <your code here>
            //triggerEnd
   ```

```
1. Write your code
   1. Use the syntax $atat_varName for case variables instead of @@varName.  The $atat_ will be swapped with @@ by @generate.php
1. Add break points and run your script (F5)
1. To generate the trigger code open @generate.php in your folder and run (f5).  The trigger code will no be in a folder called /triggerOut under your folder.  Copy and paste your trigger code into workflow.

Note - this project is provided as is.  The ee functions used here a simulations of the actual functions called by ellucian workflow.
The names arguments match however the data returned might not be exactly the same.
```
