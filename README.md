# FAIR Data Point

Castor’s FAIR Data Point allows researchers to share high-level metadata and data of their clinical trials and
registries according to existing, community-adopted standards: the [DCAT standard](https://www.w3.org/TR/vocab-dcat-2/) and [FAIR Data Point metadata
specification](https://github.com/FAIRDataTeam/FAIRDataPoint-Spec). This metadata includes textual descriptions of and contact and licensing information for catalogs (groups
of datasets), datasets (data collections which are available for access or download in one or more representations) and
distributions (specific available form of a dataset). 


## Setting up development environment

### Requirements

To be able to run the FAIR Data Point application locally you need to have the following applications installed:

- docker: <https://docs.docker.com/install/>
- docker-compose: <https://docs.docker.com/compose/install/>

### Before booting the application

- Copy the `.env.dist` file into the same folder with the new name `.env.local`. 
- Paste the secrets (marked with `[Paste from 1Password]`) from the `ENG - FDP Limited Access` 1Password note in the `.env.local` file.


- Add the following entry to `/etc/hosts`:
    ```
    127.0.0.1       fdp.castoredc.local
    ```


### Managing the local environment
To boot the application locally run:

```bash
docker-compose up --build -d
```

You can now access the FAIR Data Point via <https://fdp.castoredc.local>.

To stop the environment run:

```bash
docker-compose stop
```

To destroy all the containers:

```bash
docker-compose down
```


### Building the UI
To build the UI and watch for changes while developing, run:

```bash
yarn watch
```

To build the UI for production use, run:

```bash
yarn build
```

To upgrade Matter (Castor's Design System), run:
```bash
yarn upmatter
```
