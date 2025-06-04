
<template>
    <div class="container">
        <h1>Person BMI Web App</h1>

        <nav class="main-nav">
            <RouterLink to="addperson">Add Person</RouterLink> |
            <RouterLink to="listperson">View All</RouterLink> |
            <RouterLink to="viewperson">View One</RouterLink> |
            <RouterLink to="editperson">Edit</RouterLink> |
            <RouterLink to="deleteperson">Delete</RouterLink> |
            <RouterLink to="stats">Bmi Text</RouterLink>
        </nav>

    </div>
    <RouterView @person-added="handleAddPerson" :personList='personList' @update-person="handleUpdatePerson"
        @delete-person="handleDeletePerson"></RouterView>
 
</template>

<script>

export default {
    name: 'App',
    components: {
       
    },
    data() {
        return {
            personList: [

            ],
        }
    },
    methods: {
        handleAddPerson(entry) {
            this.personList.push(entry)
        },
        handleUpdatePerson(index, updatedPerson) {
            this.personList.splice(index, 1, updatedPerson); // ✅ update from child
        },
        handleDeletePerson(index) {
            this.personList.splice(index, 1)
        }
    },
    mounted() {
        fetch('http://localhost:8085/person')
            .then(response => response.json())
            .then(data => {
                this.personList = data;
            })
            .catch(error => {
                console.error('Error fetching person list:', error);
            });
    }

}



</script>

<style></style>
