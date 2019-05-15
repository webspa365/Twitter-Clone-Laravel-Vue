<template>
  <div class='tweet' @click='open_replies($event)'>
    <div v-if='tweet.retweetedBy !== ""' class='retweeted'>
      <i class='fa fa-retweet'></i> {{tweet.retweetedBy}} retweeted
    </div>
    <div v-if='tweet.replyTo' class='replyingTo replying'>
      <span class='replying' :replied='replyTo.id' @click='open_replied($event)'>
        Replying to @{{this.replyTo.username}}
      </span>
    </div>
    <router-link class='avatar' :to="'/profile?user='+tweet.username">
      <img v-if='tweet.avatar' class='avatarImg' :src='tweet.avatar' onerror="this.style.display='none'" />
      <i v-else class='fa fa-user'></i>;
    </router-link>
    <div class='info'>
      <span class='name'>{{tweet.name}}</span>
      <span class='username'>
        <router-link :to="'/profile?user='+tweet.username">@{{tweet.username}}</router-link>
      </span>
      <span class='date'>・{{date}}</span>
      <div class='toggle' @click='open_menu()'>
        <i class='fa fa-angle-down'></i>
        <TweetMenu v-show='menu' :tweet='tweet' />
      </div>
    </div>
    <div class='content'>
      <p>{{tweet.tweet}}</p>
    </div>
    <div class='icons'>
      <div class='reply' @click='open_reply_dialog()'>
        <i class='fa fa-comment-o'></i><span class='span'>{{tweet.replies}}</span>
      </div>
      <div class='retweet' @click='post_retweet()'>
        <i :class="'fa fa-retweet '+retweet"></i><span class='span'>{{tweet.retweets}}</span>
      </div>
      <div class='like' @click='post_like()'>
        <i :class="'fa fa-heart-o '+heart"></i><span class='span'>{{tweet.likes}}</span>
      </div>
      <div class='chart'><i class='fa fa-bar-chart'></i></div>
    </div>
  </div>
</template>

<script>
require('./Tweet.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import TweetMenu from '@/components/tweets/TweetMenu';

export default {
  name: 'Tweet',
  props: ['data'],
  components: {
    TweetMenu
  },
  data: function () {
    return {
      msg: '',
      //date: from_now(new Date(this.data.created_at).getTime()),
      //date: get_now(this.data.time),
      date: get_now(new Date(this.data.created_at).getTime()),
      replyTo: (this.data.replyTo) ? JSON.parse(this.data.replyTo) : '',
      tweet: this.data,
      menu: false,
      retweet: (this.data.retweeted) ? 'active' : '',
      heart: (this.data.liked) ? 'active' : ''
    }
  },
  methods: {
    post_data: function(e) {
      var comp = this;
    },

    open_replied(e) {
      console.log('open_replied()');
      var id = e.target.getAttribute('replied');
      console.log('id='+id);
      if(id) {
        this.get_replies(id);
        _('.replies').style.display = 'block';
      }
    },

    open_replies(e) {
      console.log('open_replies()');
      var classList = e.target.classList.toString();
      if(classList.search('(fa|toggle|span|avatarImg|replying)') === -1) {
        if(_('.deleteDialog').style.display === 'block') return;
        this.get_replies(this.tweet.id);
        _('.replies').style.display = 'block';
      }
    },

    get_replies(id) {
      console.log('get_replies() id='+id);
      this.$store.commit('replied', null);
      this.$store.commit('replies', []);
      if(localStorage.getItem('jwtToken')) {
        http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      }
      http.get('/api/replies/'+id)
      .then((res) => {
        console.log('/api/replies='+JSON.stringify(res.data));
        if(res.data.success) {
          var avatars = res.data.avatars;
          // set replied
          if(res.data.replied) {
            var replied = res.data.replied;
            if(avatars[replied.username]) {
              var ext = avatars[replied.username].split('.').pop();
              replied.avatar = server.url+'/storage/media/'+replied.userId+'/avatar/thumbnail.'+ext;
            }
            this.$store.commit('replied', replied);
          }
          // set replies
          if(res.data.replies) {
            var replies = res.data.replies;
            for(var r of replies) {
              if(avatars[r.username]) {
                var ext = avatars[r.username].split('.').pop();
                r.avatar = server.url+'/storage/media/'+r.userId+'/avatar/thumbnail.'+ext;
              }
            }
            this.$store.commit('replies', replies);
          }
        } else {
          this.$store.commit('replied', null);
          this.$store.commit('replies', []);
        }
      });
    },

    open_reply_dialog: function() {
      //_('.replies').style.display = 'none';
      this.$store.commit('replyTo', this.tweet);
      _('.replyDialog').style.display = 'block';

    },

    open_menu: function() {
      if(this.menu) this.menu = false;
      else this.menu = true;
    },

    post_retweet: function() {
      if(!this.$store.state.loggedIn || !localStorage.getItem('jwtToken')) {
        this.$router.push('/');
        return;
      }
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/retweets', {tweetId: this.tweet.id})
      .then((res) => {
        console.log('/api/retweets='+JSON.stringify(res.data));
        if(res.data.success) {
          // update heart
          if(res.data.retweeted) this.retweet = 'active';
          else this.retweet = '';

          // update tweet likes
          this.tweet.retweets = res.data.tweetC;
        }
      });

    },

    post_like: function() {
      if(!this.$store.state.loggedIn || !localStorage.getItem('jwtToken')) {
        this.$router.push('/');
        return;
      }
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/likes', {tweetId: this.tweet.id})
      .then((res) => {
        console.log('/api/likes='+JSON.stringify(res.data));
        if(res.data.success) {
          // update heart
          if(res.data.liked) this.heart = 'active';
          else this.heart = '';

          // update tweet likes
          this.tweet.likes = res.data.tweetC;

          // update account likes
          this.$store.state.account.likes = res.data.userC;

          // update profile likes
          if(this.$store.state.profile.id === this.$store.state.account.id) {
            var profile = this.$store.state.profile;
            profile.likes = res.data.userC;
            this.$store.commit('profile', profile);
          }
        }
      });
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
