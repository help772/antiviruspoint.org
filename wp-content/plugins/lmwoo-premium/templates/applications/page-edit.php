<h1 class="wp-heading-inline"><?php echo esc_html__('Edit Application', 'license-manager-for-woocommerce'); ?></h1>
<hr class="wp-header-end">
<div id="notice" class="notice notice-success settings-error is-dismissible" style="display: none;"></div>
<div id="lmfwc-editor">
	<div class="lmfwc-row">
		<!-- Application box -->
		<div class="lmfwc-left metabox-holder">
			<div class="postbox lmfwc-postbox">
				<div class="postbox-header lmfwc-postbox-header">
					<h2 class="hndle">{{ strings.title_application }}</h2>
				</div>
				<div class="inside lmfwc-inside">
					<table class="form-table">
						<tbody>
						<tr scope="row" v-for="field in fields">
							<th scope="row">
								<label :for="field.id">{{ field.label }}<span v-if="field.required" class="text-danger">*</span></label>
							</th>
							<td>
								<input v-if="'text' === field.type" :id="field.id" v-model="currentApplication[field.id]" class="regular-text" type="text">
								<textarea v-if="'textarea' === field.type" :id="field.id" v-model="currentApplication[field.id]" class="regular-text" rows="4"></textarea>
								<select v-if="'select' === field.type" :id="field.id" v-model="currentApplication[field.id]" class="regular-text">
									<option v-for="(item, key) in (field.options === 'computed' ? options[field.id] : field.options)" v-bind:value="key">
										{{ item }}
									</option>
								</select>
								<div v-if="'gallery' === field.type" :id="field.id">
									<button class="button-secondary button-small" @click="galleryItemAdd(field.id)">+
										{{ strings.add_release }}
									</button>
									<div class="lmfwc-gallery-options">
										<div v-for="(item, key) in currentApplication[field.id]" class="lmfwc-gallery-option">
											<div class="lmfwc-gallery-option-head">
												<h3>
													{{ options[field.id][key] && options[field.id][key].title ?
													options[field.id][key].title : 'New item' }}
													<a class="lmfwc-gallery-option-remove" @click="galleryItemRemove(field.id, key)">&times;</a>
												</h3>
											</div>
											<div class="lmfwc-gallery-option-form">
												<div class="lmfwc-gallery-option-field">
													<label>{{ strings.file }}</label>
													<div class="lmfwc-gallery-option-field-wrapper lmfwc-gallery-option-field-wrapper-image" :style="galleryItemImageStyle(field.id, key)">
														<button type="button" class="dashicons-cloud-upload dashicons-before" @click="galleryItemSelect(field.id, key)">
															{{ strings.upload }}
														</button>
													</div>
												</div>
												<!-- <div class="lmfwc-gallery-option-field">
													<label>{{ strings.title }}</label>
													<div class="lmfwc-gallery-option-field-wrapper">
														<input type="text" v-model="options[field.id][key].title">
													</div>
												</div> -->
												<div class="lmfwc-gallery-option-field">
													<label>{{ strings.description }}</label>
													<div class="lmfwc-gallery-option-field-wrapper">
														<textarea v-model="options[field.id][key].description"></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<p class="description" id="tagline-description">
									<b v-if="field.required">{{ strings.required }}</b>
									<span>{{ field.description }}</span>
								</p>
							</td>
						</tr>
						</tbody>
					</table>
					<p class="submit">
						<button name="submit" id="submit" class="button button-primary" @click="applicationSave()" type="submit">
							<span class="lmfwc-loading" v-if="loading.application_form"></span>
							{{ strings.update }}
						</button>
					</p>
				</div>
			</div>
		</div>

		<!-- Releases box -->
		<div class="lmfwc-right metabox-holder">
			<div class="postbox lmfwc-postbox">
				<div class="postbox-header lmfwc-postbox-header">
					<h2 class="hndle">{{ strings.title_releases }}</h2>
					<div class="handle-actions hide-if-no-js">
						<a @click="releaseAddForm" href="#"><span class="dashicons-before dashicons-plus"></span></a>
					</div>
				</div>
				<div class="inside lmfwc-postbox-inside">
					<table class="lmfwc-list-table">
						<thead>
						<tr>
							<th></th>
							<th>{{ strings.version }}</th>
							<th>{{ strings.date }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody v-if="releases.length > 0">
						<tr v-for="release in releases">
							<td>#{{ release.id }}</td>
							<td>{{ release.version }}</td>
							<td>{{ release.created_at }}</td>
							<td>
								<ul class="lmfwc-actions">
									<li>
										<a href="#" @click="releaseEditForm(release.id)" class="dashicons-before dashicons-edit"></a>
									</li>
									<li>
										<a href="#" @click="releaseDelete(release)" class="dashicons-before dashicons-trash"></a>
									</li>
								</ul>
							</td>
						</tr>
						</tbody>
						<tbody v-else>
						<tr>
							<td colspan="3">{{ strings.no_releases_found }}</td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>

	<!-- Release add lmfwc-modal -->
	<div :class="`${modals.release ? 'lmfwc-modal lmfwc-open' : 'lmfwc-modal'}`">
		<div class="lmfwc-modal__overlay jsOverlay"></div>
		<div class="lmfwc-modal__container">
			<div class="lmfwc-form-row lmfwc-form-row-header">
				<h3>{{ currentRelease.id === 'new' ? strings.new_release : strings.edit_release.replace('%s', '#' + currentRelease.id) }}</h3>
			</div>
			<div class="lmfwc-form-row" v-for="field in allReleaseFields">
				<label :for="field.id">
					{{ field.label }}
					<span v-if="field.required && (currentRelease.id === 'new' || !currentRelease[field.id])" class="text-danger">*</span>
				</label>
				<div v-if="'text' === field.type">
					<input :id="field.id" v-model="currentRelease[field.id]" class="regular-text" type="text">
				</div>
				<div v-if="'file' === field.type">
					<p class="lmfwc-file" v-if="currentRelease[field.id] && !(currentRelease[field.id] instanceof File)">
						{{ '#' + currentRelease[field.id] }}
						<span v-if="currentRelease.download && currentRelease.download_type === 'local'"> - {{ currentRelease.download['name'] + ' (' + currentRelease.download['size_readable']  + ')' }}</span>
					</p>
					<input :id="field.id" type="file" @change="releaseGetFile">
				</div>
				<div v-if="'textarea' === field.type">
					<textarea :id="field.id" v-model="currentRelease[field.id]" class="regular-text" rows="4"></textarea>
				</div>
				<p class="description" id="tagline-description">
					<b v-if="field.required && (currentRelease.id === 'new' || !currentRelease[field.id])">{{
						strings.required }}</b>
					<span v-html="field.description"></span>
				</p>
			</div>
			<div class="lmfwc-form-row lmfwc-form-row-footer">
			<button type="submit" @click="releaseSave()" class="button-primary">
				<span class="lmfwc-loading" v-if="loading.release_form"></span>
				{{ currentRelease.id === 'new' ? strings.create : strings.update }}
			</button>
		</div>
			<button class="lmfwc-modal__close" @click="modals.release = false;">&#10005;</button>
		</div>
	</div>

</div>

<script>
	Vue.config.devtools = true;
	new Vue({
		el: "#lmfwc-editor",
		created: function () {
			this.strings = LMFWC_PRO_EDITOR.strings;
			this.types = LMFWC_PRO_EDITOR.types;
			this.fields = LMFWC_PRO_EDITOR.application.fields;
			this.releaseFields = LMFWC_PRO_EDITOR.release.fields;
			this.releaseMetaFields = LMFWC_PRO_EDITOR.release.meta_fields;
			this.releases = LMFWC_PRO_EDITOR.release.list;
			this.urls = LMFWC_PRO_EDITOR.urls;
			if (LMFWC_PRO_EDITOR.application.current) {
				this.currentApplication = LMFWC_PRO_EDITOR.application.current;
				if (!Array.isArray(this.currentApplication.gallery)) {
					
					this.currentApplication.gallery = [];
				}
			}
		},
		computed: {
			options: function () {
				var final = {
					type: {},
					stable_release_id: {
						'-1': LMFWC_PRO_EDITOR.strings.none,
					},
					gallery: [],
				};
				for (var i in this.types) {
					final['type'][i] = this.types[i];
				}
				for (var i in this.releases) {
					final['stable_release_id'][this.releases[i].id] = this.releases[i].version;
				}
				if (this.currentApplication) {
					final['gallery'] = this.currentApplication.gallery;
				}
				return final;
			},
			allReleaseFields: function () {
				var fields = this.releaseFields;
				var meta_fields = this.releaseMetaFields.hasOwnProperty(this.currentApplication.type) ? this.releaseMetaFields[this.currentApplication.type] : [];
				if (meta_fields) {
					fields = fields.concat(meta_fields);
				}
				return fields;
			},
		},
		data: {
			urls: {},
			types: [],
			fields: [],
			strings: [],
			releases: [],
			releaseFields: [],
			releaseMetaFields: [],
			releaseMode: '',
			cache: {},
			currentApplication: {
				name: null,
				type: null,
				description: null,
				stable_release_id: null,
				documentation: null,
				support: null,
				gallery: []
			},
			currentRelease: {},
			modals: {
				release: false,
			},
			loading: {
				release_form: false,
				application_form: false,
			}
		},
		methods: {
			getVar(name) {
				return this[name];
			},
			showAlert(msg) {
				alert(msg);
			},
			toFormData: function (model, form, namespace) {
				let formData = form || new FormData();
				for (var propertyName in model) {
					if (!model.hasOwnProperty(propertyName)) {
						continue;
					}
					var formKey = namespace ? `${namespace}[${propertyName}]` : propertyName;
					if (model[propertyName] instanceof Date) {
						formData.append(formKey, model[propertyName].toISOString());
					} else if (model[propertyName] instanceof Array) {
						model[propertyName].forEach((element, index) => {
							const tempFormKey = `${formKey}[${index}]`;
							this.toFormData(element, formData, tempFormKey);
						});
					} else if (model[propertyName] instanceof File) {
						formData.append(formKey, model[propertyName]);
					} else if (typeof model[propertyName] === 'object') {
						this.toFormData(model[propertyName], formData, formKey);
					} else {
						formData.append(formKey, model[propertyName].toString());
					}
				}
				return formData;
			},
			post: function (url, data, onSuccess, onError, onStart, onFinish, onProgress) {
				if (onStart) {
					onStart();
				}
				var http = new XMLHttpRequest();
				http.open('POST', url, true)
				http.send(this.toFormData(data));
				http.onreadystatechange = function () {
					if (http.readyState === 4) {
						var responseText = http.responseText;
						if (http.status === 200) {
							if (onSuccess) {
								var responseJSON;
								try {
									responseJSON = JSON.parse(responseText);
								} catch (e) {
									responseJSON = null;
								}
								onSuccess(responseJSON ? responseJSON : responseText);
							}
						} else {
							if (onError) {
								onError(responseJSON ? responseJSON : responseText);
							}
						}
						if (onFinish) {
							onFinish();
						}
					}
				}
				if (onProgress) {
					http.onprogress = function (event) {
						if (event.lengthComputable) {
							var percentComplete = (event.loaded / event.total) * 100;
							onProgress(percentComplete);
						}
					};
				}
			},
			modalsOpen: function (modal) {
				this.modals[modal] = true;
			},
			modalsClose: function (modal) {
				this.modals[modal] = false;
			},
			galleryItemAdd: function (field_id) {
				if (!Array.isArray(this.currentApplication[field_id])) {
					return;
				}
				this.currentApplication[field_id].push({
					id: null,
					url: null,
					title: '',
					description: '',
				})
			},
			galleryItemRemove: function (field_id, key) {
				if (!Array.isArray(this.currentApplication[field_id])) {
					return;
				}
				this.currentApplication[field_id].splice(key, 1);
			},
			galleryItemSelect: function (field_id, key) {
				var self = this;
				var frame = wp.media({
					title: 'Select or Upload image',
					button: {
						text: 'Use this file'
					},
					multiple: false,
					library: {
						type: 'image' // This restricts selection to images only
					}
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					console.log(attachment);
					self.currentApplication[field_id][key].id = attachment.id;
					self.currentApplication[field_id][key].url = attachment.url;
				});

				frame.open();
			},
			galleryItemImageStyle: function (field_id, key) {
				var final = {};
				if (Array.isArray(this.currentApplication[field_id])) {
					if (this.currentApplication[field_id].hasOwnProperty(key) && this.currentApplication[field_id][key].hasOwnProperty('url') && this.currentApplication[field_id][key].url) {
						final['background-image'] = 'url(' + this.currentApplication[field_id][key].url + ');';
					}
				}
				var gen = '';
				for (var i in final) {
					gen += (i + ':' + final[i] + ';');
				}
				return gen;
			},
			applicationSave() {
				var self = this;
				self.post(
					this.urls.application_update,
					this.currentApplication,
					function (response) { // Success.
						if (response.success) {
							self.showNotice( response.data.message);
						} else {
							self.showAlert( response.data.errors[0].message);
						}
					},
					function (response) { // onError.
						self.showAlert('HTTP Error.');
					},
					function () {
						self.loading.application_form = true;
					},
					function () {
						self.loading.application_form = false;
					}
				);
			},

		showNotice(message) {
				var noticeDiv = document.getElementById('notice');
				if (noticeDiv) {
				   
					var paragraph = document.createElement('p');
					var strong = document.createElement('strong');
					strong.textContent = message;
					paragraph.appendChild(strong);
					noticeDiv.innerHTML = '';
					noticeDiv.appendChild(paragraph);
					noticeDiv.style.display = 'block';
				}
			},

			releaseGetFile(event) {
				var id = event.srcElement.id;
				console.log(event.target.files);
				for (var i in this.allReleaseFields) {
					if (this.allReleaseFields[i].id === id) {
						this.currentRelease[id] = event.target.files[0];
					}
				}

			},
			releaseAddForm() {
				if (!this.currentApplication) {
					this.showAlert(this.strings.no_application_found);
					return;
				}
				var release = {};
				for (var i in this.allReleaseFields) {
					release[this.allReleaseFields[i].id] = null;
				}
				release.id = 'new';
				release.application_id = this.currentApplication.id;
				this.currentRelease = release;
				this.modalsOpen('release');
				this.releaseMode = 'create';
			},
			releaseEditForm(id) {
				var release = null;
				for (var i in this.releases) {
					if (this.releases[i].id === id) {
						release = this.releases[i];
						break;
					}
				}
				if (!release) {
					this.showAlert(this.strings.no_application_found);
					return;
				}
				this.currentRelease = release;
				this.modalsOpen('release');
				this.releaseMode = 'edit';
			},
			releaseIndex() {
				var self = this;
				this.post(
					this.urls.release_index,
					{application_id: this.currentApplication.id},
					function (response) { // Success.
						if (response.success) {
							self.releases = response.data.records;
						}
					},
					function (response) {
						self.showAlert('HTTP Error.');
					}
				);
			},
			releaseSave() {
				var self = this;
				this.post(
					self.urls.release_save,
					self.currentRelease,
					function (response) { // Success.
						if (response.success) {
							self.releaseIndex();
							self.showNotice(response.data.message);
							if ('create' === self.releaseMode) {
								self.releaseMode = '';
								self.currentRelease = {};
								self.modalsClose('release');
							}
						} else {
							self.showAlert(response.data.errors[0].message);
						}
					},
					function (response) { // onError.
						self.showAlert('HTTP Error.');
					},
					function () {
						self.loading.release_form = true;
					},
					function () {
						self.loading.release_form = false;
					}
				);
			},
			releaseDelete(item) {
				var self = this;
				if (confirm(self.strings.confirm_deletion)) {
					self.post(
						self.urls.release_delete,
						item,
						function (response) { // Success.
							if (response.success) {
								self.releaseIndex();
								self.showNotice(response.data.message);
							} else {
								self.showAlert(response.data.errors[0].message);
							}
						},
						function (response) { // onError.
							self.showAlert('HTTP Error.');
						}
					);
				}
			}
		},
	})
</script>
