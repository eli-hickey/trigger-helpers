# trigger-helpers

Steps to use

1. Download and install git, visual studio code, and docker desktop if needed.

2. In VS code add ['Remote - Containers' extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

3. From command prompt at the location of your preferred working directory clone the repository

   ```
   git clone https://github.com/eli-hickey/trigger-helpers.git
   ```

4. Now move to the trigger-helpers directory created by git clone and open the folder in vs code.  This can be done from the command prompt or the 'Open With Code' option in the right click in your file explore.

   ```
   cd trigger-helpers
   code .
   ```

5. Launch the container using the command pallet command 'Remote-Containers: Reopen in Container'

6. Rename env.example to .env and add your api key and region to it (US  = 'com', Canada = 'ca' ...)



You are now ready to get your ethos data with some ee functions.