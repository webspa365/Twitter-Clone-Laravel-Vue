<template>
  <div class='timeline'>
    <div class='header'>
      <div class='input'>
        <input class='form-control' placeholder="What's happenning?" />
        <i class='fa fa-image'></i>
      </div>
      <div class='avatar'>
        <img src={this.props.account.avatar} onerror="this.style.display='none'" />
      </div>
    </div>
    <div class='body'>
      <ul>{tweets}</ul>
    </div>
    <div class='footer' onClick={() => {this.show_more()}}>
      <span>Show more tweets...</span>
    </div>
  </div>
</template>

<script>
require('./Timeline.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'Timeline',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function(e) {
      var comp = this;
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
