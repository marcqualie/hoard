{% extends 'layouts/default.volt' %}

{% block content %}
<div class="container">

    <h1>{{ bucket.name }} - Events</h1>

    <table class="table table-striped">
        <tbody>
        {% for event in bucket.getEvents() %}
            <tr>
                <td>{{ date('Y-m-d H:i', event.created_at.sec) }}</td>
                <td>{{ event.name }}</td>
                <td><code>{{ event.data | json_encode }}</code></td>
            </tr>
        {% endfor %}
        </tbody>
    </tale>

</div>
{% endblock %}
