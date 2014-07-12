{% extends 'layouts/default.volt' %}

{% block content %}

<div class="container">

  <table class="table table-striped">
    <thead>
      <th class="text-center" width="20%">ID</th>
      <th class="text-center">Name</th>
      <th class="text-center" width="13%">Trend</th>
      <th class="text-center" width="13%">Events</th>
      <th class="text-center" width="13%">Storage</th>
    </thead>
    <tbody>
    {% for bucket in buckets %}
      <tr>
        <td class="text-center"><code>{{ bucket.getId() }}</code></td>
        <td class="text-center"><a href="/buckets/<?= $bucket->getId() ?>">{{ bucket.name }}</a></td>
        <td class="text-center">0 0 0</td>
        <td class="text-center"><a href="{{ url(['for': 'bucket-events', 'id': bucket.getId() ]) }}">{{ bucket.getEvents() | length }}</td>
        <td class="text-center">0 <span class="text-muted">b</span></td>
      </tr>
    {% endfor %}
  </table>

</div>

{% endblock %}
