function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tab");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
    sessionStorage.setItem('activeTab', tabName);
}

document.addEventListener('DOMContentLoaded', function() {
    const activeTabId = sessionStorage.getItem('activeTab') || 'record-video';
    const tabLink = document.querySelector(`.tab[onclick*="'${activeTabId}'"]`);
    if (tabLink) {
        tabLink.click();
    }

    // Drag and drop
    const dropArea = document.getElementById('drop-area');
    const videoInput = document.getElementById('video');
    const fileNameDisplay = document.getElementById('file-name');

    if (dropArea) {
        dropArea.addEventListener('click', (event) => {
            event.stopPropagation();
            videoInput.click();
        });

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.style.borderColor = '#5a67d8';
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.style.borderColor = '#dee2e6';
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.style.borderColor = '#dee2e6';
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                videoInput.files = files;
                handleFileUpload({ target: { files } });
            }
        });
    }

    if (videoInput) {
        videoInput.addEventListener('change', handleFileUpload);
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        const videoPreview = document.getElementById('video-preview');
        if (file) {
            fileNameDisplay.textContent = file.name;
            const objectURL = URL.createObjectURL(file);
            videoPreview.src = objectURL;
            videoPreview.style.display = 'block';
            videoPreview.onloadedmetadata = function() {
                if (videoPreview.duration > 60) {
                    alert('La vidéo ne doit pas dépasser 60 secondes.');
                    videoInput.value = ''; // Reset file input
                    videoPreview.style.display = 'none';
                    fileNameDisplay.textContent = '';
                } 

            };
        }
    }

    // Handle video recording
    const startBtn = document.getElementById('start-record-btn');
    const stopBtn = document.getElementById('stop-record-btn');
    const useBtn = document.getElementById('use-video-btn');
    const liveVideo = document.getElementById('live-video');
    const recordedVideoPreview = document.getElementById('recorded-video-preview');
    const timerDiv = document.getElementById('recording-timer');
    const recordedVideoDataInput = document.getElementById('recorded_video_data');

    let mediaRecorder;
    let recordedChunks = [];
    let timerInterval;
    let seconds = 0;
    let recordedBlob = null;

    // Fonction de diagnostic des caméras
    async function diagnoseCameraIssues() {
        const diagnosticInfo = {
            browserSupport: !!navigator.mediaDevices && !!navigator.mediaDevices.getUserMedia,
            isSecureContext: window.isSecureContext,
            protocol: window.location.protocol,
            userAgent: navigator.userAgent,
            devices: [],
            videoDevices: [],
            cameraAccessTest: null,
            permissionStatus: null
        };

        try {
            if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                const devices = await navigator.mediaDevices.enumerateDevices();
                diagnosticInfo.devices = devices;
                diagnosticInfo.videoDevices = devices.filter(device => device.kind === 'videoinput');
            }
        } catch (err) {
            console.error('Erreur lors du diagnostic:', err);
        }

        // Test d'accès à la caméra
        try {
            if (navigator.permissions) {
                const permission = await navigator.permissions.query({ name: 'camera' });
                diagnosticInfo.permissionStatus = permission.state;
            }
        } catch (err) {
            console.warn('Impossible de vérifier les permissions:', err);
        }

        // Test rapide d'accès à la caméra
        try {
            const testStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            diagnosticInfo.cameraAccessTest = 'success';
            testStream.getTracks().forEach(track => track.stop());
        } catch (err) {
            diagnosticInfo.cameraAccessTest = err.name || err.message;
        }

        console.log('Diagnostic des caméras:', diagnosticInfo);
        return diagnosticInfo;
    }

    if (startBtn) {
        startBtn.addEventListener('click', async () => {
            const cameraErrorDiv = document.getElementById('camera-permission-error');
            cameraErrorDiv.style.display = 'none';
            try {
                // Effectuer un diagnostic complet
                const diagnostic = await diagnoseCameraIssues();

                // Vérifier si l'API MediaDevices est supportée
                if (!diagnostic.browserSupport) {
                    throw new Error('MediaDevices API not supported');
                }

                // Vérification de sécurité désactivée - accès libre à la caméra

                // Vérifier s'il y a des caméras disponibles
                if (diagnostic.videoDevices.length === 0) {
                    throw new Error('NoVideoDevicesFound');
                }

                // Essayer d'abord avec vidéo et audio
                let stream;
                try {
                    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                } catch (audioError) {
                    console.warn('Impossible d\'accéder au microphone, essai avec vidéo seulement:', audioError);
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    } catch (videoError) {
                        console.error('Impossible d\'accéder à la caméra:', videoError);
                        throw videoError;
                    }
                }
                liveVideo.srcObject = stream;
                mediaRecorder = new MediaRecorder(stream, { mimeType: 'video/webm' });

                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };

                mediaRecorder.onstart = () => {
                    recordedBlob = null;
                    recordedChunks = [];
                    startBtn.disabled = true;
                    stopBtn.disabled = false;
                    timerDiv.style.display = 'block';
                    seconds = 0;
                    timerDiv.textContent = '0s';
                    timerInterval = setInterval(() => {
                        seconds++;
                        timerDiv.textContent = `${seconds}s`;
                        if (seconds >= 60) {
                            stopBtn.click();
                        }
                    }, 1000);
                };

                mediaRecorder.onstop = () => {
                    clearInterval(timerInterval);
                    timerDiv.style.display = 'none';
                    recordedBlob = new Blob(recordedChunks, { type: 'video/webm' });
                    const videoUrl = URL.createObjectURL(recordedBlob);
                    recordedVideoPreview.src = videoUrl;
                    recordedVideoPreview.style.display = 'block';
                    useBtn.style.display = 'block';
                    liveVideo.style.display = 'none';
                    liveVideo.srcObject.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();
            } catch (err) {
                console.error("Erreur d'accès à la caméra: ", err);
                let errorMessage = "<strong>Erreur d'accès à la caméra.</strong> ";
                let troubleshootingTips = "";
                
                switch(err.name || err.message) {
                    case 'NotFoundError':
                    case 'NoVideoDevicesFound':
                        errorMessage += "Aucune caméra n'a été trouvée sur votre appareil.";
                        troubleshootingTips = `
                            <div class="mt-3">
                                <strong>Solutions possibles :</strong>
                                <ul class="mt-2">
                                    <li>Vérifiez qu'une caméra est bien connectée à votre ordinateur</li>
                                    <li>Si vous utilisez une caméra externe, assurez-vous qu'elle est branchée et allumée</li>
                                    <li>Redémarrez votre navigateur et réessayez</li>
                                    <li>Vérifiez les pilotes de votre caméra dans le gestionnaire de périphériques</li>
                                    <li>Essayez avec un autre navigateur (Chrome, Firefox, Safari)</li>
                                </ul>
                            </div>`;
                        break;
                    case 'NotAllowedError':
                        errorMessage += "L'accès à la caméra a été refusé. Veuillez l'autoriser dans les paramètres de votre navigateur.";
                        troubleshootingTips = `
                            <div class="mt-3">
                                <strong>Comment autoriser la caméra :</strong>
                                <ul class="mt-2">
                                    <li>Cliquez sur l'icône de caméra dans la barre d'adresse</li>
                                    <li>Sélectionnez "Autoriser" pour ce site</li>
                                    <li>Rechargez la page et réessayez</li>
                                    <li>Dans Chrome : Paramètres > Confidentialité et sécurité > Paramètres du site > Caméra</li>
                                </ul>
                            </div>`;
                        break;
                    case 'NotReadableError':
                        errorMessage += "Un problème matériel empêche l'accès à la caméra. Assurez-vous qu'elle n'est pas utilisée par une autre application.";
                        troubleshootingTips = `
                            <div class="mt-3">
                                <strong>Solutions possibles :</strong>
                                <ul class="mt-2">
                                    <li>Fermez toutes les autres applications utilisant la caméra (Zoom, Skype, etc.)</li>
                                    <li>Redémarrez votre ordinateur</li>
                                    <li>Vérifiez que la caméra fonctionne dans d'autres applications</li>
                                </ul>
                            </div>`;
                        break;

                    case 'MediaDevices API not supported':
                        errorMessage += "Votre navigateur ne supporte pas l'accès à la caméra.";
                        troubleshootingTips = `
                            <div class="mt-3">
                                <strong>Solutions possibles :</strong>
                                <ul class="mt-2">
                                    <li>Mettez à jour votre navigateur vers la dernière version</li>
                                    <li>Utilisez un navigateur moderne (Chrome, Firefox, Safari, Edge)</li>
                                    <li>Vérifiez que JavaScript est activé</li>
                                </ul>
                            </div>`;
                        break;
                    default:
                        errorMessage += "Une erreur inattendue est survenue. Veuillez réessayer.";
                        troubleshootingTips = `
                            <div class="mt-3">
                                <strong>Solutions générales :</strong>
                                <ul class="mt-2">
                                    <li>Rechargez la page et réessayez</li>
                                    <li>Redémarrez votre navigateur</li>
                                    <li>Essayez avec un autre navigateur</li>
                                    <li>Vérifiez votre connexion internet</li>
                                </ul>
                            </div>`;
                }
                
                cameraErrorDiv.innerHTML = errorMessage + troubleshootingTips;
                cameraErrorDiv.className = 'camera-error';
                cameraErrorDiv.style.display = 'block';
             }
         });

         // Bouton de diagnostic
         const diagnoseBtn = document.getElementById('diagnoseCameras');
         if (diagnoseBtn) {
             diagnoseBtn.addEventListener('click', async () => {
                 const cameraErrorDiv = document.getElementById('camera-permission-error');
                 const diagnostic = await diagnoseCameraIssues();
                 
                 let diagnosticMessage = "<strong>Diagnostic des caméras :</strong><br>";
                 
                 // Support du navigateur
                 diagnosticMessage += `<br><strong>Support du navigateur :</strong> ${diagnostic.browserSupport ? '✅ Supporté' : '❌ Non supporté'}`;
                 
                 // Contexte sécurisé
                 diagnosticMessage += `<br><strong>Connexion sécurisée :</strong> ${diagnostic.isSecureContext ? '✅ HTTPS' : '⚠️ HTTP'} (${diagnostic.protocol})`;
                 
                 // Permissions
                 if (diagnostic.permissionStatus) {
                     const permissionIcon = diagnostic.permissionStatus === 'granted' ? '✅' : 
                                           diagnostic.permissionStatus === 'denied' ? '❌' : '⚠️';
                     diagnosticMessage += `<br><strong>Permission caméra :</strong> ${permissionIcon} ${diagnostic.permissionStatus}`;
                 }
                 
                 // Test d'accès
                 if (diagnostic.cameraAccessTest) {
                     const accessIcon = diagnostic.cameraAccessTest === 'success' ? '✅' : '❌';
                     const accessText = diagnostic.cameraAccessTest === 'success' ? 'Accès réussi' : diagnostic.cameraAccessTest;
                     diagnosticMessage += `<br><strong>Test d'accès :</strong> ${accessIcon} ${accessText}`;
                 }
                 
                 // Navigateur
                 const browserInfo = diagnostic.userAgent.includes('Chrome') ? 'Chrome' : 
                                   diagnostic.userAgent.includes('Firefox') ? 'Firefox' : 
                                   diagnostic.userAgent.includes('Safari') ? 'Safari' : 
                                   diagnostic.userAgent.includes('Edge') ? 'Edge' : 'Autre';
                 diagnosticMessage += `<br><strong>Navigateur :</strong> ${browserInfo}`;
                 
                 // Périphériques détectés
                 diagnosticMessage += `<br><strong>Caméras détectées :</strong> ${diagnostic.videoDevices.length}`;
                 
                 if (diagnostic.videoDevices.length > 0) {
                     diagnosticMessage += `<ul class="mt-2">`;
                     diagnostic.videoDevices.forEach((device, index) => {
                         const deviceName = device.label || `Caméra ${index + 1}`;
                         diagnosticMessage += `<li>${deviceName} (ID: ${device.deviceId.substring(0, 8)}...)</li>`;
                     });
                     diagnosticMessage += `</ul>`;
                 } else {
                     diagnosticMessage += `<br><span style="color: #d32f2f;">❌ Aucune caméra détectée</span>`;
                 }
                 
                 // Tous les périphériques
                 const audioDevices = diagnostic.devices.filter(d => d.kind === 'audioinput');
                 diagnosticMessage += `<br><strong>Microphones détectés :</strong> ${audioDevices.length}`;
                 
                 // Recommandations
                 if (!diagnostic.browserSupport) {
                     diagnosticMessage += `<br><br><strong style="color: #d32f2f;">⚠️ Votre navigateur ne supporte pas l'accès aux caméras.</strong>`;
                 } else if (diagnostic.videoDevices.length === 0) {
                     diagnosticMessage += `<br><br><strong style="color: #d32f2f;">⚠️ Aucune caméra n'a été détectée sur votre système.</strong>`;
                 } else {
                     diagnosticMessage += `<br><br><strong style="color: #2e7d32;">✅ Votre système semble compatible avec l'enregistrement vidéo.</strong>`;
                 }
                 
                 cameraErrorDiv.innerHTML = diagnosticMessage;
                  cameraErrorDiv.className = 'camera-error diagnostic';
                  cameraErrorDiv.style.display = 'block';
             });
         }
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', () => {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
            stopBtn.disabled = true;
        });
    }

    if (useBtn) {
        useBtn.addEventListener('click', () => {
            if (recordedBlob) {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const base64data = reader.result;
                    recordedVideoDataInput.value = base64data;
                    alert('La vidéo enregistrée a été ajoutée au formulaire.');
                    useBtn.disabled = true;
                    useBtn.innerHTML = '<i class="fas fa-check"></i> Vidéo ajoutée';

                    // Clear the file input if a recorded video is used
                    videoInput.value = '';
                    const videoPreview = document.getElementById('video-preview');
                    videoPreview.src = '';
                    videoPreview.style.display = 'none';
                    fileNameDisplay.textContent = '';
                };
                reader.readAsDataURL(recordedBlob);
            }
        });
    }

    // Gestion du formulaire d'enregistrement
    const recordForm = document.querySelector('#record-video form');
    if (recordForm) {
        recordForm.addEventListener('submit', function(event) {
            const recordedVideo = recordedVideoDataInput.value.trim() !== '';
            if (!recordedVideo) {
                alert('Veuillez enregistrer une vidéo avant de soumettre.');
                event.preventDefault();
                return;
            }
            const submitButton = event.submitter;
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            }
        });
    }

    // Gestion du formulaire d'importation
    const uploadForm = document.querySelector('#upload-video form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(event) {
            const videoFile = videoInput.files.length > 0;
            if (!videoFile) {
                alert('Veuillez sélectionner une vidéo à importer.');
                event.preventDefault();
                return;
            }
            const submitButton = event.submitter;
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            }
        });
    }
});

// Make openTab globally accessible
window.openTab = openTab;