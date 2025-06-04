<template>
    <div>
        <h3>Person List</h3>
        <ul>
            <li v-for="(p, i) in personList" :key="i">
                <div class="person-card">
                    <img v-if="p.photoUrl" :src="p.photoUrl" alt="photo" class="person-photo" />
                    <div>
                        <strong>[{{ p.id }}] {{ p.name }}</strong> (Age: {{ p.age }})<br>
                        Weight: {{ p.weight }} kg, Height: {{ p.height }} cm<br>
                        BMI: {{ p.bmi }}
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'PersonList',
    data() {
        return {
            personList: [] // Initial empty list
        };
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
};
</script>