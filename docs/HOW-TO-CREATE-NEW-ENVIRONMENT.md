## How to create a new environment

* create an environment under repository settings
* populate the secrets [ref](./REPOSITORY-SECRETS.md)
* save the secrets into the secrets repo
* create the respective subdomain (output: folder)
* create the respective FTP user (use the folder from the subdomain)
* modify the FE build script - to have a build for the specific environment
* align the vite plugins to insert the proper environment into config.json
* add the env environment to the train yaml
* wire in the proper build script into the fe deployment yaml
* deploy the code
* manually create the chat messages, chat members and hall of fame json files
* update the documentation (repository envs, envs)