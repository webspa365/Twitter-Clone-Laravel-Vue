<template>
  <div class='logIn'>
    <header>
      <i class='fa fa-twitter'></i>
      <h1>Log In to Twitter</h1>
    </header>
    <form>
      <div class="form-group">
        <label>Username:</label>
        <input type="text" class="form-control" id="username" v-model='username' />
      </div>
      <div class="form-group">
        <label>Password:</label>
        <input type="password" class="form-control" id="password" v-model='password' />
      </div>
      <div class="form-group">
        <label class='message'>{{msg}}</label>
        <input type="button" class="form-control button" id="login" value="Log In" @click='log_in()' />
      </div>
    </form>
    <div class='toSignUp'>
      <p>New to Twitter? <span @click='switch_form("SignUp")'>Sign up now »</span></p>
    </div>
  </div>
</template>

<script>
require('./LogIn.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'LogIn',
  props: ['switch_form'],
  data: function () {
    return {
      msg: '',
      username: 'user',
      password: 'password'
    }
  },
  methods: {
    log_in: function() {
      _('.loader').style.display = 'block';
      var data = {
        username: this.username,
        password: this.password
      }
      http.post('/api/login', data)
      .then((res) => {
        console.log('/api/login='+JSON.stringify(res.data));
        _('.loader').style.display = 'none';
        if(res.data.success) {
          localStorage.setItem('jwtToken', res.data.token);
          var user = res.data.user;
          user.dir = server.url+'/storage/media/'+user.id;
          this.$store.commit('loggedIn', true);
          this.$store.commit('account', user);
          this.$store.commit('profile', user);
          if(user.avatar) {
            var avatar = user.avatar;
            var ext = avatar.split('.').pop();
            var icon = user.dir+'/avatar/thumbnail.'+ext;
            this.$store.commit('navIcon', icon);
          } else {
            this.$store.commit('navIcon', '');
          }
          this.$router.push('/profile?user='+user.username);
        } else {
          this.msg = res.data.msg;
        }
      }).catch((err) => {
        console.log(JSON.stringify(err));
        this.msg = 'catch((err) => {})';
        _('.loader').style.display = 'none';
      });
    },

    on_change: function() {

    },

    change_username: function() {

    },

    change_password: function() {

    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
