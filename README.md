## Setup

After cloning the project run `docker-compose up --build`.
<br>
it might take the app several minutes to boot up, specially since a lot of data processing will take place in order to 
insert data from the large `json` file to the database.
<br>
The API endpoints are available on port `8080` of the `localhost`

## Tests

Run the unit tests by executing this command inside the `shpmnt_app` docker service: 
```bash
phpunit tests/Unit/Services
```

## Documentation

Open API documentation is available in `swagger.yml` file in the root of the project.
You can also [visit online](https://app.swaggerhub.com/apis/red7626/Shipment/0.1#/default/get_api_v1_carriers__id__shipments).

### Developer Note

* In order to ease the process of project setup I intentionally removed `.env` files from `.gitignore`.
* The database structure is inside `database.sql` file at the root of the project.
* I've tried to deploy the app on a cloud base platform, unfortunately due to political reasons none of them provide services for my region.    