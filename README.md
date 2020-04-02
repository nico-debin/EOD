# EOD API

Interact with EOD API and save responses to the database.

## Usage
#####  Retrive Stock Details from EOD API.
`php artisan stocks:get US AMZN`

##### Download Stock Details from EOD API. Data is saved in the database.
`php artisan stocks:save US --all --file=us-stocks.txt --silent`

--all: All tickers.

--file= A file inside the `storage` directory with a list of tickers to download.

--silent: For cronjobs. Remove this param for prompts.
 