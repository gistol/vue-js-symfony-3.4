<template>
    <div>
        <form v-on:submit.prevent="handleSubmit" enctype="multipart/form-data">
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" v-model="title"/>

            <label for="content">Contenu</label>
            <input type="text" id="content" name="content"/>

            <label for="image">Image</label>
            <input type="file" id="image" name="image"/>

            <input type="submit"/>
        </form>
    </div>
</template>

<script>

    export default {
        name: 'admin',

        data() {
            return {
                title: ""
            }
        },

        methods: {
            handleSubmit() {

                if (this.title.length < 3) {
                    alert("Le titre doit contenir au moins 3 caractères.");
                }
            },
        },

        beforeRouteEnter(to, from, next) {

            function userAuthenticated() {
                if (!localStorage.token) {
                    return false;
                }

                return new Promise((resolve, reject) => {
                    const req = window.XMLHttpRequest ?
                        new XMLHttpRequest() :
                        new ActiveXObject("Microsoft.XMLHTTP");

                    req.open("POST", '/vue-js-symfony-3.4/web/app_dev.php/checkToken');
                    req.addEventListener("load", () => {
                        if (req.status >= 200 && req.status < 400) {
                            resolve(JSON.parse(req.responseText));
                        } else {
                            reject(req.statusText);
                        }
                    });

                    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                    req.send("token=" + localStorage.token);
                });
            }

            if(to.meta.requiresAuth){
                userAuthenticated().then((data) => {
                    if (!data) {
                        next('/login');
                    } else {
                        next();
                    }
                }).catch((err) => {
                    console.log(err);
                });
            }

            next();
        },
    }
</script>