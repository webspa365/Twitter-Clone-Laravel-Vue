<template>
  <div class='signUp'>
    <header>
      <i class='fa fa-twitter'></i>
      <h1>Create Your Account</h1>
    </header>
    <form>
      <div class="form-group">
        <label>Username: <span id='for_username'></span></label>
        <input type="text" class="form-control" id="username" name='username' v-model='username' />
      </div>
      <div class="form-group">
        <label>Email address: <span id='for_email'></span></label>
        <input type="email" class="form-control" id="email" name='email' v-model='email' />
      </div>
      <div class="form-group">
        <label>Password: <span id='for_password'></span></label>
        <input type="password" class="form-control" id="password" name='password' v-model='password' />
      </div>
      <div class="form-group">
        <label>Confirm password: <span id='for_confirmation'></span></label>
        <input type="password" class="form-control" id="confirmation" name='confirmation' v-model='confirmation' />
      </div>
      <div class="form-group">
        <label><span id='for_post' class='message'>{{msg}}</span></label>
        <input type="button" class="form-control button" id="submit" value="Sign Up" @click='sign_up()' />
      </div>
    </form>
    <div class='toLogIn'>
      <p>If you have an account, <span @click='switch_form("LogIn")'>Log In »</span></p>
    </div>
  </div>
</template>

<script>
require('./SignUp.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'SignUp',
  props: ['switch_form'],
  data: function () {
    return {
      msg: '',
      username: '',
      email: '',
      password: '',
      confirmation: ''
    }
  },
  methods: {
    sign_up: function(e) {
      _('.loader').style.display = 'block';
      var data = {
        username: this.username,
        email: this.email,
        password: this.password,
        password_confirmation: this.confirmation
      }
      http.post('/api/register', data)
      .then((res) => {
        console.log('/users/signup='+JSON.stringify(res.data));
        _('.loader').style.display = 'none';
        if(res.data.success) {
          localStorage.setItem('jwtToken', res.data.token);
          var user = res.data.user;
          this.$store.state.loggedIn = true;
          this.$store.state.account = user;
          this.$store.state.profile = user;
          this.$router.push('/profile?user='+user.username);
        } else {
          this.msg = res.data.msg;
        }
      }).catch((err) => {
        console.log(err);
        this.msg = 'catch((err) => {})';
        _('.loader').style.display = 'none';
      });
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
