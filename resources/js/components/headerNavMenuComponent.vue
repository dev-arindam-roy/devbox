<template>
    <nav class="navbar navbar-expand-lg navbar-dark navbg">
        <router-link class="navbar-brand" :to="{name: 'homeRoute'}">
            <img src="/images/devbox_64.png" style="width:32px;">
            <span><strong>DevBox</strong></span>
        </router-link>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <router-link class="nav-link" :to="{name: 'homeRoute'}">Home <span class="sr-only">(current)</span></router-link>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Create
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <router-link class="dropdown-item" :to="{name: 'addBoxRoute'}">Add PostBox</router-link>
                        <router-link class="dropdown-item" :to="{name: 'addCategoryRoute'}">Add Category</router-link>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">My Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
    export default {
        mounted() {
        }
    }
</script>
