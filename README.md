
## PHP Library for K&H Payment Gateway

[![Tests status][test status image]][test status] [![Static Analysis][phpstan status image]][phpstan status] [![codecov](https://codecov.io/gh/connorhu/khvpos/branch/2.x/graph/badge.svg?token=6B0AVBCWCP)](https://codecov.io/gh/connorhu/khvpos)

[API documentation HU](https://github.com/khpos/Payment-gateway_HU) | [API documentation EN](https://github.com/khpos/Payment-gateway_EN)

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
| oneclick/echo     | yes                 |
| oneclick/init     | yes                 |
| oneclick/process  | yes                 |
| applepay/echo     | yes                 |
| applepay/init     | yes                 |
| applepay/process  | yes                 |
| googlepay/echo    | yes                 |
| googlepay/init    | yes                 |
| googlepay/process | yes                 |

  [test status image]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml/badge.svg?branch=master
  [test status]: https://github.com/connorhu/khvpos/actions/workflows/tests.yml
  [phpstan status image]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml/badge.svg
  [phpstan status]: https://github.com/connorhu/khvpos/actions/workflows/static-analysis.yml
  [master coverage image]: https://codecov.io/gh/connorhu/khvpos/branch/master/graph/badge.svg
  [master coverage]: https://codecov.io/gh/connorhu/khvpos/branch/master
  [echo example]: https://github.com/connorhu/khvpos/blob/2.x/examples/01-echo-request.php
