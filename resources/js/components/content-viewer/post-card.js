/**
 * Post Card Alpine.js Component
 * 
 * Handles individual post card operations:
 * - Content editing and display
 * - Media upload/generation
 * - Social media publishing
 * - Facebook/Instagram page selection
 */

/**
 * Factory function to create the post card component
 * @param {Object} config - Configuration with routes, content data, brands, etc.
 * @param {number} postIndex - Index of this post in the batch
 * @returns {Object} - Alpine.js component data object
 */
export function createPostCardComponent(config, postIndex) {
    const postData = config.postsData[postIndex] || {};

    return {
        index: postIndex,
        showMediaOptions: false,
        imageUrl: null,
        isUploading: false,
        isGenerating: false,
        isRegenerating: false,
        isPublishing: false,
        isEditing: false,
        isPublished: postData.published || false,
        publishResult: null,
        showPublishModal: false,
        selectedPlatforms: [],
        scheduleDate: new Date().toISOString().slice(0, 16),
        facebookPages: [],
        selectedFacebookPage: null,
        isFacebookConnected: config.isFacebookConnected,
        isFetchingPages: false,
        showPageModal: false,
        postNow: true,
        rawContent: postData.raw || '',
        htmlContent: postData.html || '',

        // Banana Pro Image Creator Modal
        showImageCreatorModal: false,
        imageFormat: 'realistic',
        imagePrompt: '',
        posterText: '',
        selectedAssetUrl: null,
        selectedBrandId: null,
        mediaAssets: [],
        isLoadingAssets: false,
        brands: config.brands || [],
        showCopyToast: false,

        init() {
            const visuals = config.visuals || [];
            if (visuals && visuals[this.index]) {
                this.imageUrl = visuals[this.index];
            }
            this.$watch('imageUrl', (val) => {
                if (val) this.persistVisual(this.index);
            });

            // Load remembered Facebook Page
            const savedPage = localStorage.getItem('arch_ai_fb_page');
            if (savedPage) {
                try {
                    this.selectedFacebookPage = JSON.parse(savedPage);
                } catch (e) { console.error('Error loading saved page', e); }
            }

            // Silent refresh to ensure latest permissions/IG IDs
            if (this.isFacebookConnected) {
                this.refreshPageData();
            }
        },

        refreshPageData() {
            if (!this.selectedFacebookPage) return;

            fetch('/social-planner/facebook-pages')
                .then(res => res.ok ? res.json() : { pages: [] })
                .then(data => {
                    const pages = data.pages || [];
                    if (pages.length > 0) {
                        const freshPage = pages.find(p => p.id === this.selectedFacebookPage.id);
                        if (freshPage) {
                            this.selectedFacebookPage = freshPage;
                            localStorage.setItem('arch_ai_fb_page', JSON.stringify(freshPage));
                        }
                    }
                })
                .catch(() => { });
        },

        toggleEdit() {
            this.isEditing = !this.isEditing;
            if (!this.isEditing) {
                this.htmlContent = this.formatForDisplay(this.rawContent);
            }
        },

        persistVisual(idx) {
            fetch(config.saveVisualUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify({ index: idx, image_url: this.imageUrl })
            });
        },

        fetchFacebookPages() {
            this.isFetchingPages = true;
            this.showPageModal = true;

            fetch('/social-planner/facebook-pages')
                .then(res => res.json())
                .then(data => {
                    this.facebookPages = data.pages || [];
                    this.isFacebookConnected = this.facebookPages.length > 0;

                    if (this.facebookPages.length === 0) {
                        if (!this.isFacebookConnected) {
                            alert('No connection found. Please link your Facebook account in the Social Planner.');
                            this.showPageModal = false;
                        } else {
                            alert('No Facebook pages found. Ensure you have granted "Manage Pages" permissions.');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error communicating with the Social Engine.');
                    this.showPageModal = false;
                })
                .finally(() => { this.isFetchingPages = false; });
        },

        fetchInstagramPages() { this.fetchFacebookPages(); },
        fetchLinkedinPages() { alert('LinkedIn Company Page selection coming soon.'); },
        fetchTwitterPages() { alert('Twitter Account selection coming soon.'); },

        selectPage(page) {
            this.selectedFacebookPage = page;
            localStorage.setItem('arch_ai_fb_page', JSON.stringify(page));
            this.showPageModal = false;
            if (!this.selectedPlatforms.includes('facebook')) {
                this.selectedPlatforms.push('facebook');
            }
        },

        isVideo(url) {
            if (!url) return false;
            return /\.(mp4|mov|avi|wmv|webm)$/i.test(url);
        },

        copyContent() {
            const textToCopy = this.rawContent;
            navigator.clipboard.writeText(textToCopy).then(() => {
                this.showCopyToast = true;
                setTimeout(() => { this.showCopyToast = false; }, 2000);
            }).catch(err => {
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showCopyToast = true;
                setTimeout(() => { this.showCopyToast = false; }, 2000);
            });
        },

        triggerUpload() { this.$refs.fileInput.click(); },

        handleUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.isUploading = true;

            if (file.type.startsWith('image/')) {
                this.compressImage(file, (compressedBlob) => {
                    this.performUpload(compressedBlob, file.name);
                });
            } else {
                this.performUpload(file, file.name);
            }
        },

        performUpload(fileBlob, fileName) {
            const formData = new FormData();
            formData.append('file', fileBlob, fileName);
            fetch('/content-creator/upload-media', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': config.csrfToken, 'Accept': 'application/json' }
            })
                .then(async res => {
                    if (!res.ok) {
                        if (res.status === 413) throw new Error('File is too large.');
                        const text = await res.text();
                        try { return JSON.parse(text); } catch { throw new Error(res.statusText); }
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        this.imageUrl = data.url;
                        this.showMediaOptions = false;
                    } else { alert(data.message || 'Upload failed'); }
                })
                .catch(err => { console.error(err); alert(err.message || 'Upload error'); })
                .finally(() => { this.isUploading = false; });
        },

        compressImage(file, callback) {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const MAX_WIDTH = 1920;
                    const MAX_HEIGHT = 1920;
                    let width = img.width;
                    let height = img.height;
                    if (width > height) {
                        if (width > MAX_WIDTH) { height *= MAX_WIDTH / width; width = MAX_WIDTH; }
                    } else {
                        if (height > MAX_HEIGHT) { width *= MAX_HEIGHT / height; height = MAX_HEIGHT; }
                    }
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    canvas.toBlob((blob) => { callback(blob); }, 'image/jpeg', 0.8);
                };
            };
        },

        openImageCreator() {
            let cleanPrompt = this.rawContent
                .replace(/#\w+/g, '')
                .replace(/\n+/g, ' ')
                .trim()
                .substring(0, 300);
            this.imagePrompt = cleanPrompt;
            this.posterText = cleanPrompt.substring(0, 80);

            this.showImageCreatorModal = true;
            this.showMediaOptions = false;

            if (this.mediaAssets.length === 0) {
                this.loadMediaAssets();
            }
        },

        loadMediaAssets() {
            this.isLoadingAssets = true;
            fetch('/media-assets?limit=20', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken }
            })
                .then(res => res.json())
                .then(data => { this.mediaAssets = data.assets || []; })
                .catch(err => console.error('Failed to load assets:', err))
                .finally(() => { this.isLoadingAssets = false; });
        },

        generateAdvancedImage() {
            if (!this.imagePrompt.trim()) {
                alert('Please enter a prompt for the image.');
                return;
            }

            this.isGenerating = true;
            this.showImageCreatorModal = false;

            const payload = {
                prompt: this.imagePrompt,
                format: this.imageFormat,
                poster_text: this.imageFormat === 'poster' ? this.posterText : null,
                reference_asset_url: this.imageFormat === 'asset-reference' ? this.selectedAssetUrl : null,
                brand_id: this.imageFormat === 'poster' ? this.selectedBrandId : null
            };

            fetch('/content-creator/generate-media', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify(payload)
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.imageUrl = data.url;
                        this.showMediaOptions = false;
                    } else {
                        alert(data.message || 'Generation failed');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Generation error. Please try again.');
                })
                .finally(() => { this.isGenerating = false; });
        },

        generateImage() { this.openImageCreator(); },

        regenerateText() {
            this.isRegenerating = true;
            fetch('/content-creator/regenerate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify({ content_id: config.contentId, current_text: this.rawContent })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.rawContent = data.new_text;
                        this.htmlContent = this.formatForDisplay(data.new_text);
                    } else { alert('Failed to redo'); }
                })
                .catch(e => console.error(e))
                .finally(() => { this.isRegenerating = false; });
        },

        formatForDisplay(text) {
            let clean = text
                .replace(/\*\*/g, '')
                .replace(/\*/g, '')
                .replace(/^#+\s+/gm, '')
                .replace(/`/g, '');
            return clean.replace(/\n/g, '<br>');
        },

        openPublishModal() { this.showPublishModal = true; },

        confirmPublish() {
            if (this.selectedPlatforms.length === 0) {
                alert('Please select at least one platform.');
                return;
            }

            if (this.selectedPlatforms.includes('instagram') && !this.imageUrl) {
                alert('Instagram posts require an image. Please add visuals before publishing.');
                return;
            }

            let fbPageId = null;
            let fbPageToken = null;
            let igAccountId = null;

            if (this.selectedPlatforms.includes('facebook') || this.selectedPlatforms.includes('instagram')) {
                if (this.selectedFacebookPage) {
                    fbPageId = this.selectedFacebookPage.id;
                    fbPageToken = this.selectedFacebookPage.access_token;

                    if (this.selectedFacebookPage.instagram_business_account) {
                        igAccountId = this.selectedFacebookPage.instagram_business_account.id;
                    }
                } else {
                    if (!confirm('You have selected a Meta platform but no specific Page. Continue?')) { return; }
                }
            }

            if (this.selectedPlatforms.includes('instagram') && !igAccountId) {
                if (!confirm('The selected Facebook Page does not have a linked Instagram Business account. Instagram posting will fail. Continue?')) { return; }
            }

            this.isPublishing = true;
            fetch('/content-creator/publish', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': config.csrfToken },
                body: JSON.stringify({
                    content_id: config.contentId,
                    segment_index: this.index,
                    final_text: this.rawContent,
                    image_url: this.imageUrl,
                    platforms: this.selectedPlatforms,
                    scheduled_at: this.postNow ? 'now' : this.scheduleDate,
                    facebook_page_id: fbPageId,
                    facebook_page_token: fbPageToken,
                    instagram_account_id: igAccountId
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.isPublished = true;
                        this.publishResult = data.message;
                        if (data.results?.facebook && !data.results.facebook.success) {
                            this.publishResult += "\n\nNote: Facebook post failed: " + data.results.facebook.error;
                        }
                        if (data.results?.instagram && !data.results.instagram.success) {
                            this.publishResult += "\n\nNote: Instagram post failed: " + data.results.instagram.error;
                        }
                    } else {
                        alert('Publishing failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .finally(() => { this.isPublishing = false; this.showPublishModal = false; });
        }
    };
}
