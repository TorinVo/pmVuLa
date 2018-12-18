<template>
  <!-- DIRECT CHAT -->
  <div class="card direct-chat card-outline" :class="[boxColor, directChatColor]">
    <div class="card-header">
      <h3 class="card-title">{{ (selectedContact) ? selectedContact.name : 'Select a Contact' }}</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-toggle="tooltip" title="Contacts" data-widget="chat-pane-toggle">
          <i class="fa fa-comments"></i>
        </button>
        <button type="button" class="btn btn-tool" data-widget="closechat"><i class="fa fa-times"></i></button>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="card-body">
      <!-- Conversations are loaded here -->
      <div class="direct-chat-messages">
        <va-direct-chat-item
          v-for="(item, index) in talklist"
          :key="index"
          :name="item.name"
          :date="item.created_at"
          :profileImage="item.photo"
          :message="item.text"
          :isMine="item.isMine"
        ></va-direct-chat-item>

      </div>
      <!--/.direct-chat-messages-->

      <!-- Contacts are loaded here -->
      <div class="direct-chat-contacts">
        <ul class="contacts-list">
          <va-direct-chat-contact
            :contacts="contacts" @selected="startConversationWith"
          ></va-direct-chat-contact>
        </ul>
        <!-- /.contatcts-list -->
      </div>
      <!-- /.direct-chat-pane -->
    </div>
    <!-- /.box-body -->
    <div class="card-footer">
      <form action="#" method="post">
        <div class="input-group">
          <input type="text" name="message" :placeholder="placeholder" class="form-control">
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary btn-flat">Send</button>
              </span>
        </div>
      </form>
    </div>
    <!-- /.box-footer-->
  </div>
  <!--/.direct-chat -->

</template>

<script>
import VADirectChatItem from './VADirectChatItem.vue'
import VADirectChatContact from './VADirectChatContact.vue'
export default {
    name: 'va-direct-chat',
    beforeMount() {
        this.$store.dispatch('actionUserFetch')
    },
    props: {
        theme: {
            type: String,
            default: 'primary'
        },
        title: {
            type: String
        },
        placeholder: {
            type: String,
            default: 'Type Message ...'
        }
    },
    methods: {
        startConversationWith(contact) {
            axios.get(`/api/message/${contact.id}`)
            .then((response) => {
                this.talklist = response.data;
                this.selectedContact = contact;
            })
        }
    },
    data() {
        return {
            selectedContact: null,
            talklist: []
        };
    },
    computed: {
        contacts() {
            return this.$store.state.storeUser.users
        },
        badgeColor () {
        switch (this.theme) {
            case 'primary':
            return 'bg-primary'
            case 'success':
            return 'bg-green'
            case 'warning':
            return 'bg-yellow'
            case 'danger':
            return 'bg-red'
            default:
            return 'bg-light-blue'
        }
        },
        boxColor () {
        switch (this.theme) {
            case 'primary':
            case 'success':
            case 'warning':
            case 'danger':
            case 'vue':
            return `card-${this.theme}`
            default:
            return 'card-primary'
        }
        },
        directChatColor () {
        switch (this.theme) {
            case 'primary':
            case 'success':
            case 'warning':
            case 'danger':
            case 'vue':
            return `direct-chat-${this.theme}`
            default:
            return 'direct-chat-primary'
        }
        }
  },
  created () {
  },
  components: {
    'va-direct-chat-item': VADirectChatItem,
    'va-direct-chat-contact': VADirectChatContact
  }
}
</script>