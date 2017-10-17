<template>
    <div class="row" v-bind:class="{'has-error':error}">
        <div class="col-md-2">
            <input type="file" ref="file" v-on:input="upload" style="display:none;"/>

            <div class="btn-group">
                <button type="button" class="btn btn-primary" v-on:click="openDialog" title="Upload">
                    <span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span>
                </button>

                <button type="button" class="btn btn-danger" 
                        v-bind:disabled="!uFile"
                        v-on:click="remove"
                        title="Remove">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>

                <button type="button" class="btn btn-secondary" 
                        v-if="firstFile"
                        v-on:click="reset"
                        title="Reset">
                    <span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>
                </button>
            </div> 

            <p class="help-block">{{ error }}</p>
        </div>
        <div class="col-md-2">
            <img v-if="showThumbnail" v-bind:src="uFile.url" class="img-thumbnail">

            <a v-if="showDownload" v-bind:href="uFile.url" class="btn btn-info" title="download" target="_blank">
                <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
            </a>

            <div  v-if="showProgress" class="progress">
                <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                     v-bind:style="{'width':percentCompleted + '%'}"
                     v-bind:aria-valuenow="percentCompleted">                         
                </div>
            </div> 
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    class UploadedFile {
        constructor(id, token, url, client) {
            this.id = id;
            this.token = token;
            this.url = url;
            this.client = client;
        }
    }

    export default {
        props: ['value'],
        data() {
            var uFile = this.value ? new UploadedFile(this.value.id, this.value.token, this.value.url, this.value.client) : null;
            return {
                firstFile: JSON.parse(JSON.stringify(uFile)),
                uFile,
                percentCompleted: 0,
                error: null
            };
        },
        computed: {
            showProgress() {
                return this.percentCompleted > 0 && this.percentCompleted < 100;
            },
            showThumbnail() {
                return !this.showProgress && this.uFile && this.uFile.client.mime.match(/^image\//);
                ;
            },
            showDownload() {
                return !this.showProgress && !this.showThumbnail;
            }
        },
        methods: {
            reset() {
                this.error = null;
                this.$emit('input', this.uFile = JSON.parse(JSON.stringify(this.firstFile)));
            },
            remove() {
                this.error = null;
                this.$emit('input', this.uFile = null);
            },
            openDialog() {
                this.$refs.file.click();
            },
            upload() {
                this.error = null;
                var inputName = 'file';
                var data = new FormData();
                data.append(inputName, this.$refs.file.files[0]);

                axios.post('/upload', data, {
                    onUploadProgress: (progressEvent) => {
                        this.percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    }
                }).then((res) => {
                    this.$emit('input', this.uFile = new UploadedFile(res.data.id, res.data.token, res.data.url, res.data.client));
                }).catch((err) => {
                    if (err.response.status === 422) {
                        this.error = err.response.data.errors[inputName][0];

                    }
                    if (err.response.status === 413) {
                        this.error = 'Payload Too Large';
                    }
                    return console.error(err.message);
                });
            }
        }
    }
</script>
