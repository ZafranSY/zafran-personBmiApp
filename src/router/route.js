import { createRouter,createWebHistory } from "vue-router";
import AddPerson from "@/components/AddPerson.vue";
import EditPerson from "@/components/EditPerson.vue"
import DeletePerson from "@/components/DeletePerson.vue"
import ListPersons from "@/components/ListPersons.vue";
import ViewPerson from "@/components/ViewPerson.vue";
import BmiText from "@/components/BmiText.vue";

const router=createRouter({
    history: createWebHistory(),
    routes:[
        {path:'/listperson',component:ListPersons},
        {path:'/addperson',component:AddPerson},
        {path:'/editperson',component:EditPerson},
        {path:'/deleteperson',component:DeletePerson},
        {path:'/listperson',component:ListPersons},
        {path:'/viewperson',component:ViewPerson},
        {path:'/stats',component:BmiText},
    ]
});

export default router