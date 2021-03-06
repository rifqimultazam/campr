<template>
    <div id="app" :class="[{ bg: bgRoutes.indexOf(this.$route.name) >= 0 }, themeClass]">
        <flash-message></flash-message>
        <pulse-loader
                :color="color"
                :size="size"
                v-if="$wait.any"/>
        <sidebar :user="localUser" v-if="localUserAvailable" />
        <div class="page" v-if="localUserAvailable">
            <navigation :user="localUser"/>
            <router-view />
        </div>
    </div>
</template>
<script>
    import Navigation from './components/_layout/Navigation';
    import Sidebar from './components/_layout/Sidebar';
    import {mapActions, mapGetters} from 'vuex';
    import {PulseLoader} from 'vue-spinner/dist/vue-spinner.min.js';

    export default {
        name: 'app',
        components: {
            PulseLoader,
            Navigation,
            Sidebar,
        },
        methods: {
            ...mapActions([
                'getUserInfo',
                'syncUser',
                'syncTeam',
            ]),
        },
        created() {
            this
                .getUserInfo()
                .then(
                    () => {
                        setTimeout(() => {
                            this.localUserAvailable = true;
                        }, 256);
                    },
                    () => {
                        setTimeout(() => {
                            this.localUserAvailable = true;
                        }, 256);
                    }
                )
            ;
            this.syncUser();
            this.syncTeam();
        },
        computed: {
            ...mapGetters([
                'user',
                'loader',
                'localUser',
                'locale',
            ]),
            themeClass() {
                return this.localUser.theme === 'light' ? 'light-theme': '';
            },
        },
        data() {
            return {
                localUserAvailable: false,
                bgRoutes: ['projects-create-1', 'projects-create-2', 'projects-create-3'],
            };
        },
    };
</script>

<style lang="scss">
    @import '~theme/variables';
    @import 'css/_mixins.scss';

    html, body {
        margin: 0;
        min-height: 100vh;
    }

    body {
        font-size: 12px;
        line-height: 1.5em;
    }

    .v-spinner {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: $mainColor;
        z-index: 99;
    }

    .tablet {
        display: none;
    }

    ul {
        margin-left: 20px;
        list-style-type: disc;
    }

    p {
        margin: 0;
    }

    a {
        color: inherit;
        text-decoration: none;

        &:hover, &:active, &:focus {
            text-decoration: none;
            color: inherit;
        }
    }

    .notification-balloon {
        display: block;
        height: 20px;
        min-width: 20px;
        border-radius: 10px;
        background: $secondColor;
        color: $mainColor;
        position: absolute;
        text-align: center;
        line-height: 22px;
        padding: 0 4px 0 5px;

        @media screen and (max-width: 768px) {
            width: 16px;
            height: 16px;
            line-height: 16px;
        }
    }

    .dropdown-menu {
        color: $lightColor;
        background: $darkColor;
        width: 100%;
        margin: 0;
        @include border-radius(0);
        border-top: 1px solid $fadeColor;
        box-shadow: 0 2px 20px -2px $blackColor;
        padding: 0;

        li {
            a {
                display: block;
                color: inherit;
                text-transform: uppercase;
                font-size: 11px;
                letter-spacing: 0.1em;
                padding: 14px 12px 11px;

                &:hover {
                    color: $lighterColor;
                    background-color: $middleColor;
                }
            }
        }
    }

    .new-box {
        width: 25%;
        padding: 15px;

        a {
            text-transform: uppercase;
            text-align: center;
            min-height: 200px;
            border: 1px dashed $secondColor;
            color: $secondColor;
            display: table;
            width: 100%;
            height: 100%;
            @include transition(all, 0.2s, ease);

            span {
                display: table-cell;
                vertical-align: middle;
            }
        }

        &:hover {
            a {
                color: $secondDarkColor;
                border-color: $secondDarkColor;
            }
        }
    }

    .second-col-bg {
        background: $secondColor !important;
    }

    .warning-col-bg {
        background: $warningColor !important;
    }

    .danger-col-bg {
        background: $dangerColor !important;
    }

    .no-select {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    #app {
        font-family: 'Poppins', sans-serif;
        color: $lighterColor;
        height: 100%;
        padding: 0 0 0 210px;
        min-height: 100vh;

        @media screen and (max-width: 768px) {
            padding: 0 0 0 75px;
        }

        .page {
            padding: 0 20px;
            background: $mainColor;
            min-height: 100vh;
        }
    }
</style>
