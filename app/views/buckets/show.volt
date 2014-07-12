{% extends 'layouts/default.volt' %}

{% block content %}
<div class="container">

    <div class="page-header">
        <h2>{{ bucket.name }}</h2>
    </div>


    <div class="btn-group">
        <a href="{{ url([ 'for': 'bucket-events', 'id': bucket.getId() ]) }}" class="btn btn-primary">View Events ({{ bucket.getEvents() | length }})</a>
    </div>

</div>
{% endblock %}
