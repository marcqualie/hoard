<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Hoard</title>
    <link rel="stylesheet" href="/assets/app.css"/>
  </head>
  <body>

    {{ flashSession.output() }}

    {% block content %}{% endblock %}

    <script src="/assets/jquery.js"></script>
    <script src="/assets/bootstrap.js"></script>
    <script src="/assets/app.js"></script>

  </body>
</html>
