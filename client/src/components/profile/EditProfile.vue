<template>
  <div class='editProfile'>
    <div class='bg'>
      <img id='img_bg' :src='this.bgSrc' />
      <div class='message'>
        <i class='fa fa-camera'></i><br />
        <span>Add a header photo</span>
        <input id='input_bg' class='form-control' type='file' name='bg' @change='change_bg($event)' />
      </div>
    </div>
    <div class='nav'>
      <div class='container'>
        <div class='avatar'>
          <img id='img_avatar' :src='this.avatarSrc' />
          <div class='message'>
            <i class='fa fa-image'></i><br />
            <span>Change your profile photo</span>
          </div>
          <input id='input_avatar' class='form-control' type='file' name='avatar' @change='change_avatar($event)' />
        </div>
        <button class='btn btn-default' @click='post_data()'>Save changes</button>
        <button class='btn btn-default' @click='cancel_edit()'>Cancel</button>
      </div>
    </div>

    <div class='main container'>
      <div class='row'>
        <div class='left col-lg-3'>
          <div class='info'>
            <label>Name:</label>
            <input class='name form-control' type='text' v-model='name' />
            <label>Username:</label>
            <input class='username form-control' type='text' v-model='username' />
            <label>Email:</label>
            <input class='email form-control' type='text' v-model='email' />
            <label>Bio:</label>
            <textarea class='bio form-control' type='text' v-model='bio' />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
require('./EditProfile.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'EditProfile',
  data: function () {
    return {
      profile: {},
      bg: '',
      bgSrc: '',
      avatar: '',
      avatarSrc: '',
      name: '',
      username: '',
      email: '',
      bio: ''
    }
  },
  methods: {
    post_data: function() {
      if(!this.$store.state.loggedIn && !localStorage.getItem('jwtToken')) {
        console.log('Error: Not logged in.');
        return;
      }
      if(!this.username || !this.email) {
        console.log('Error: Username or email is empty.');
        return;
      }
      console.log('post_data() username='+this.username);
      _('.loader').style.display = 'block';
      var fd = new FormData();
      fd.append('name', this.name);
      fd.append('username', this.username);
      fd.append('email', this.email);
      fd.append('bio', this.bio);
      fd.append('bg', this.bg);
      fd.append('avatar', this.avatar);
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http({
        method: 'post',
        url: '/api/profile/'+this.username,
        data: fd,
        config: {headers: {'Content-Type': 'multipart/form-data'}}
      })
      .then((res) => {
        console.log('/api/profile='+JSON.stringify(res.data));
        _('.loader').style.display = 'none';
        if(res.data.success) {
          var user = res.data.user;
          user.dir = server.url+'/storage/media/'+user.id;
          this.$store.commit('account', user);
          this.$store.commit('profile', user);
          if(user.avatar) {
            var ext = user.avatar.split('.').pop();
            this.$store.commit('navIcon', user.dir+'/avatar/thumbnail.'+ext);
          }
          this.$router.push('/profile?user='+user.username);
        }
      })
      .catch((err) => {
        console.log(err);
        _('.loader').style.display = 'none';
      });

    },
    cancel_edit: function() {
      this.$router.push('/profile?user='+this.$store.state.profile.username);
    },
    change_bg(e) {
      console.log(e.target.files[0]);
      this.bg = e.target.files[0];
      this.bgSrc = URL.createObjectURL(e.target.files[0]);
      var img = new Image();
      img.src = this.bgSrc;
      img.onload = () => {
        //console.log(img.naturalWidth +'/'+ img.naturalHeight);
        if((img.naturalWidth/img.naturalHeight) > 4) {
          var bg = _('.editProfile .bg img');
          bg.style.width = 'auto';
          bg.style.height = '100%';
        }
      }
    },
    change_avatar(e) {
      this.avatar = e.target.files[0];
      this.avatarSrc = URL.createObjectURL(e.target.files[0]);
      var img = new Image();
      img.src = this.avatarSrc;
      img.onload = () => {
        //console.log(img.naturalWidth +'/'+ img.naturalHeight);
        if(img.naturalWidth > img.naturalHeight) {
          var avatar = _('.editProfile .avatar img');
          avatar.style.width = 'auto';
          avatar.style.height = '100%';
        }
      }
    }
  },
  mounted() {
    var profile = this.$store.state.profile;
    this.profile = profile;
    this.name = profile.name;
    this.username = profile.username;
    this.email = profile.email;
    this.bio = profile.bio;
    if(profile.bg) this.bgSrc = profile.dir+'/bg/'+profile.bg;
    if(profile.avatar) this.avatarSrc = profile.dir+'/avatar/'+profile.avatar;
  },
  beforeDestroy() {

  }
}
</script>
