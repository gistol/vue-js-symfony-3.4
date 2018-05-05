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
                } else {

                }
            },
        },

        beforeRouteEnter(to, from, next) {

            if(to.meta.requiresAuth) {

                userAuthenticated().then((data) => {
                    console.log(data);
                    next();
                }).catch((err) => {
                    console.log(err);
                    next('/login');
                });

            } else {
                next();
            }
        },
    }

    function userAuthenticated() {

        return new Promise((resolve, reject) => {

            if (!localStorage.getItem('token')) {
                resolve(false);
            }

            const req = window.XMLHttpRequest ?
                new XMLHttpRequest() :
                new ActiveXObject("Microsoft.XMLHTTP");

            req.open("GET", '/vue-js-symfony-3.4/web/app_dev.php/getSessionToken');
            req.addEventListener("load", () => {

                if (req.status >= 200 && req.status < 400) {
                    const resp = JSON.parse(req.responseText);
                    if (localStorage.getItem('token') === resp) {
                        resolve('Identification réussie');
                    } else if (resp.err) {
                        reject(resp.err);
                    } else {
                        reject('Token invalide.');
                    }
                } else {
                    reject(req.statusText);
                }
            });

            req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            req.send();
        });
    }
</script>