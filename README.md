### Setup
- `git clone git@github.com:shkabo/mtt.git`
- Set your shift4 secret key and password in `.env` file
- `docker compose up`

### Tests
- `docker compose exec metricalo-test composer tests`

### Endpoint
- `POST http://localhost/api/transaction/charge/{aci|shift4}`
- Expected parameters: `cardNumber`, `cardExpiryYear`, `cardExpiryMonth`, `cardCvv`, `amount`, `currency`

```
curl -X POST http://localhost/api/transaction/charge/aci \
    -H "Content-type: application/x-www-form-urlencoded" \
    -d "cardNumber=4200000000000000" \
    -d "cardExpiryYear=2029" \
    -d "cardExpiryMonth=03" \
    -d "cardCvv=123" \
    -d "amount=12" \
    -d "currency=EUR"
```

### Command
- `docker compose exec metricalo-test php bin/console app:transaction-charge`
- To view available processors `docker compose exec metricalo-test php bin/console app:transaction-chargedocker compose exec metricalo-test php bin/console app:transaction-charge --listProcessors`

```
docker compose exec metricalo-test php bin/console app:transaction-charge aci \
--cardNumber=4200000000000000 \
--cardExpiryYear=2029 \
--cardExpiryMonth=03 \
--cardCvv=123 \
--amount=12 \
--currency=EUR
```

### Notes
For the sake of this test, some stuff were left out:
- Use DB in the project (current version is completely blank and DB is not being used regardless that it's configured in `docker-compose.yaml`)
- Use Swagger to describe API and generate docs for it
- Add more detailed validation - Responses from the external APIs have additional data that we can use to add more validation checks. Command that's added could use some more validation of the input, so that we can avoid/minimize human error