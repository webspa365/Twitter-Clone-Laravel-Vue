<template>
  <div class='messages'>
    <h1>Messsages</h1>
  </div>
</template>

<script>
require('./Messages.css');
import {http} from '@/modules/http';
import {server} from '@/modules/server';

export default {
  name: 'messages',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function() {
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
