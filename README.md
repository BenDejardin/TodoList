### Compatibility Issue with Symfony 3.1.6 and PHP 7.2.5

I'm currently facing an error while using Symfony 3.1.6 with PHP 7.2.5. The error occurs specifically during the execution of the `$form->handleRequest($request);` function. After some research, I came across a similar issue reported in the Symfony repository:

[https://github.com/symfony/symfony/issues/26291](https://github.com/symfony/symfony/issues/26291)

It appears that the solution to this problem is to upgrade to Symfony version 3.4. To address this error, I am planning to upgrade to Symfony 3.4.

**Versions in use:**
- Symfony: 3.1.6
- PHP: 7.2.5
