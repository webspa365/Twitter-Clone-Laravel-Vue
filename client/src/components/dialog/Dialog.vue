<template>
  <div className='dialog' @click='close_dialog()'>
        <div className='dialog_wrapper'>
          {{header}}
          {{body}}
          <div className='footer'>
            {{btn_1}}
            {{btn_0}}
          </div>
        </div>
      </div>
</template>

<script>
require('./Dialog.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'Dialog',
  data: function () {
    return {
      msg: '',
      header: '',
      body: '',
      btn_0: '',
      btn_1: ''
    }
  },
  methods: {
    close_dialog: function(e) {

    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
