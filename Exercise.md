# Exercise

Last month, we received the help of an intern ; Gregory. He did a great job, but he introduced some bugs into our
application. We want you to investigate and solve those issues.

* The QA department reported us an error. They usually call the root endpoint GET `/` as a healthcheck to watch the
  liveness of the platform. But it seems broken. Here is their report :

```text
Hi dev team,

Since a few weeks ago, epg-api's healthcheck endpoint is broken.

After analysis, we received the following response : {"name":"epg-api","version":"dev","test":"hello world"}

Our tests are not expecting that return, could you bring back the old format.

Here is the curl executed : curl -X GET --location "http://localhost:8080"
```

---

* Another bug has been spotted, this morning I ran the pipeline CI of the application and I saw the unit tests did not
  succeed. Could you have a look? Here is the report.

```text
There was 1 failure:

1) Tests\Unit\Storage\EpgStorageTest::test_storeEpg
Expectation failed for method name is "write" when invoked 1 time(s)
Parameter 0 for invocation League\Flysystem\FilesystemWriter::write('epg-api/test/2021/07/15/20210...son.gz', Binary String: 0x1f8b080000000...f000000, Array ()): void does not match expected value.
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
-'epg-api/test/2021/07/15/20210715_20210715180700.json.gz'
+'epg-api/test/2021/07/15/20210715_20210715060700.json.gz'

/opt/app/app/Storage/EpgStorage.php:35
/opt/app/tests/Unit/Storage/EpgStorageTest.php:34
phpvfscomposer:///opt/app/vendor/phpunit/phpunit/phpunit:97

FAILURES!
Tests: 106, Assertions: 170, Failures: 1.


Code Coverage Report Summary:
  Classes: 40.00% (4/10)     
  Methods: 58.82% (10/17)    
  Lines:   75.31% (122/162)
```

---

A new request is asked on the application. Here is the ticket :

```text
Hello dev,

We would like to add a counter into the metrics exposed by the application to see the number of errors the application throws.

Here is the feature in a Given When Then mode :

As a Monitoring Team member
When I check the exposed metrics on `http://localhost:9091/metrics`
Then I must see a counter named aepg_epg_api_error_counter
```

---

Last but not least, we would like to continue the work of our previous developer. He had introduced the publishing of
the epg payload on a queue. We don't know where he stopped his work nor the status of the feature. Could you help us to finish
the implementation ? Feel free to choose the best way to do it. Our message broker is RabbitMQ.  
