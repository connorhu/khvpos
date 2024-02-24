
## PHP Library for K&H Payment Gateway

[![Tests status][test status image]][test status] [![Static Analysis][phpstan status image]][phpstan status] [![Coverage Status][master coverage image]][master coverage]

[API documentation](https://github.com/khpos/Payment-gateway_HU)

## Support chart

| Method            | Support             |
|-------------------|---------------------|
| echo              | [yes][echo example] |
| payment/init      | yes                 |
| payment/process   | yes                 |
| payment/status    | yes                 |
| payment/reverse   | yes                 |
| payment/close     | yes                 |
| payment/refund    | yes                 |
| oneclick/echo     | no                  |
| oneclick/init     | no                  |
| oneclick/process  | no                  |
| applepay/echo     | no                  |
| applepay/init     | no                  |
| applepay/process  | no                  |
| googlepay/echo    | no                  |
| googlepay/init    | no                  |
| googlepay/process | no                  |

  [test status image]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml/badge.svg?branch=master
  [test status]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml
  [phpstan status image]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml/badge.svg
  [phpstan status]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml
  [master coverage image]: https://codecov.io/gh/connorhu/khvpos/branch/master/graph/badge.svg
  [master coverage]: https://codecov.io/gh/connorhu/khvpos/branch/master
  [echo example]: https://github.com/connorhu/khvpos/blob/examples/01-echo-request.php
