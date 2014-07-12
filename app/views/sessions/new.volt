{% extends 'layouts/basic.volt' %}

{% block content %}
<div class="container text-center">

  <div class="row">

    <div class="col-md-6 col-md-offset-3">

      <h1>Authentication</h1>

      <br/>
      {{ form('sessions', 'method': 'post') }}

          <div class="form-group">
            <label for="email">Username/Email</label>
            {{ text_field('email', 'class': 'form-control text-center') }}
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            {{ password_field('password', 'class': 'form-control text-center') }}
          </div>

          {{ submit_button('Login', 'class': 'btn btn-primary') }}

      </form>

    </div>

  </div>

</div>
{% endblock %}
