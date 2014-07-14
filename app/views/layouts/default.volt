<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Hoard</title>
    <link rel="stylesheet" href="/assets/app.css"/>
  </head>
  <body>

    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <a class="navbar-brand" href="/">Hoard</a>
        <ul class="nav navbar-nav">
          <li><a href="/buckets">Buckets ({{ authUser.getBuckets() | length }})</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="/account">{{ authUser.email }}</a></li>
          <li><a href="/logout">Logout</a></li>
        </ul>
      </div>
    </div>

    <div class="container">
      {{ flashSession.output() }}
    </div>

    {% block content %}{% endblock %}

    <div class="page-footer clearfix">
      <div class="container">
        <div class="pull-left">
          &copy; <a href="https://github.com/marcqualie/hoard">Hoard</a> by <a href="https://marcqualie.com">Marc Qualie</a>
        </div>
        <div class="pull-right">
          <span class="text-muted">
            System Load: <strong>{{ serverMetrics.getLoad() | join(' ') }}</strong>
            &middot;
            <a href="/system">{{ serverMetrics.getHostname() }}</a>
          </span>
        </div>
      </div>
    </div>

    <script src="/assets/jquery.js"></script>
    <script src="/assets/bootstrap.js"></script>
    <script src="/assets/app.js"></script>

  </body>
</html>
