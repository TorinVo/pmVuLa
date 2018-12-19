<template>
    <div class="direct-chat-contacts v-scroll" style="height: 240px;">
        <ul class="contacts-list">
            <li v-for="contact in sortedContacts" :key="contact.id" @click="selectContact(contact)">
                <img class="contacts-list-img" :src="contact.photo" alt="User Image">
                <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    {{ contact.name }}
                    <small class="contacts-list-date float-right"><small class="badge badge-danger" v-if="contact.unread">{{ contact.unread }}</small></small>
                    </span>
                    <span class="contacts-list-msg">{{contact.email}}</span>
                </div>
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