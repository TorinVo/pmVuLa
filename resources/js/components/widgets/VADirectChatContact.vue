<template>
    <div class="direct-chat-contacts">
        <ul class="contacts-list">
            <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact)">
                <a href="#">
                <img class="contacts-list-img" :src="contact.photo" alt="User Image">

                <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    {{ contact.name }}
                    <small class="contacts-list-date float-right">{{ contact.created_at }}</small>
                    </span>
                    <span class="contacts-list-msg">{{contact.unread}}</span>
                </div>
                <!-- /.contacts-list-info -->
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "DirectChatContact",
    props: {
        contacts: {}
    },
    data() {
        return {
            selected: this.contacts.length ? this.contacts[0] : null
        };
    },
    methods: {
        selectContact(contact) {
            this.selected = contact;
            this.$emit('selected', contact);
        }
    },
    computed: {
        sortedContacts() {
            return _.sortBy(this.contacts, [(contact) => {
                if (contact == this.selected) {
                    return Infinity;
                }
                return contact.unread;
            }]).reverse();
        }
    },
    created() {}
};
</script>