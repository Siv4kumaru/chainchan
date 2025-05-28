<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Knowledge Sources // ChainChan_AI</title>
<style>
    :root {
        --border-line: #444;
        --text-green-ai: #0f0;
        --text-green-user: #0f0;
        --text-green-secondary: #0a0;
        --bg-terminal: #000;
        --font-terminal: 'Courier New', Courier, monospace;
        --button-action-bg-hover: #0f0;
        --button-action-text-hover: #000;
        --status-success-color: #28a745;
        --status-error-color: #dc3545;
    }
    .filename-link {
        color: var(--text-green-user);
        text-decoration: none;
        font-family: var(--font-terminal);
        font-size: 1em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: transparent;
        border: none;
        padding: 0;
        border-radius: 3px;
        transition: color 0.2s, text-decoration 0.2s;
        cursor: pointer;
        flex-grow: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 150px;
    }
    .filename-link:hover {
        color: var(--text-green-ai);
        text-decoration: underline;
    }

    body{background-color:var(--bg-terminal);color:var(--text-green-ai);font-family:var(--font-terminal);margin:0;padding:0;height:100vh;display:flex}
    .page-container{display:flex;width:100%;height:100%}
    .sidebar{width:220px;min-width:200px;background-color:#050505;border-right:1px solid var(--border-line);padding:20px;color:var(--text-green-user);height:100%;overflow-y:auto;box-sizing:border-box}
    .sidebar h3{color:var(--text-green-ai);margin-top:0;font-size:1.2em;border-bottom:1px solid var(--border-line);padding-bottom:10px;margin-bottom:15px}
    .sidebar a,.sidebar .sidebar-item{display:block;color:var(--text-green-user);text-decoration:none;padding:10px 8px;margin-bottom:5px;border-radius:3px;transition:background-color .2s,color .2s;font-size:.95em}
    .sidebar a:hover,.sidebar .sidebar-item:hover{background-color:var(--text-green-secondary);color:var(--bg-terminal)}
    .sidebar a.active,.sidebar .sidebar-item.active{background-color:var(--text-green-ai);color:var(--bg-terminal);font-weight:bold}
    .sidebar form button.sidebar-item{width:100%;text-align:left;background:none;border:none;cursor:pointer}
    .main-content{flex-grow:1;padding:25px;overflow-y:auto;height:100%;box-sizing:border-box}
    .content-header{color:var(--text-green-ai);font-size:2em;margin-bottom:25px;text-transform:uppercase;border-bottom:1px solid var(--border-line);padding-bottom:15px}
    .actions-bar{margin-bottom:25px;display:flex;gap:12px}
    .action-button{background-color:transparent;color:var(--text-green-user);border:1px solid var(--border-line);padding:10px 18px;font-family:var(--font-terminal);cursor:pointer;transition:background-color .1s,color .1s;text-transform:uppercase;font-size:.9em}
    .action-button:hover{background-color:var(--button-action-bg-hover);color:var(--button-action-text-hover);border-color:var(--button-action-bg-hover)}
    .list-container{border:1px solid var(--border-line);padding:15px;min-height:150px;background-color:#080808}
    .list-item{padding:10px 15px;border:1px solid var(--border-line);color:var(--text-green-ai);display:flex;align-items:center;gap:10px;font-family:var(--font-terminal);margin-bottom:8px;border-radius:3px;position:relative}
    .list-item:hover{background-color:var(--text-green-secondary);color:var(--bg-terminal)}
    .list-item .details{min-width:180px;text-align:right;white-space:nowrap;font-size:.9em;color:var(--text-green-secondary); margin-left: auto;}
    .list-item:hover .details{color:var(--bg-terminal)}
    .list-item .controls-container{display:flex;align-items:center;gap:10px; margin-left: auto;}
    .role-multiselect{background-color:var(--bg-terminal);color:var(--text-green-user);border:1px solid var(--border-line);padding:6px 8px;font-family:var(--font-terminal);min-width:200px;max-width:250px;min-height:100px;max-height:150px;border-radius:3px;font-size:1em}
    .role-multiselect option{padding:6px 10px;font-size:1em;cursor:pointer}
    .role-multiselect option:checked{background-color:var(--text-green-ai);color:var(--bg-terminal)}
    .save-button{background-color:rgb(40,116,167);color:rgb(13,12,12);border:1px solid var(--border-line);padding:0 15px;font-family:var(--font-terminal);cursor:pointer;transition:background-color .1s,color .1s;height:38px;align-self:center;text-transform:uppercase;font-size:.9em;border-radius:3px}
    .save-button:disabled{opacity:.5;cursor:not-allowed}
    .save-button:hover:not(:disabled){background-color:var(--button-action-bg-hover);color:var(--button-action-text-hover);border-color:var(--button-action-bg-hover)}
    .delete-button{background-color:var(--status-error-color);color:#fff;border:1px solid #a71d2a;padding:0 15px;font-family:var(--font-terminal);cursor:pointer;transition:background-color .1s,opacity .1s;height:38px;align-self:center;text-transform:uppercase;font-size:.9em;border-radius:3px;margin-left:5px}
    .delete-button:hover{background-color:#a71d2a;opacity:.9}
    .delete-button:disabled{background-color:#ea868f;opacity:.6;cursor:not-allowed}
    .item-status-message{font-size:.85em;padding:5px 8px;border-radius:3px;min-width:120px;text-align:center;display:none}
    .item-status-message.success{background-color:rgb(40,116,167);color:#fff;border:1px solid #1e5272}
    .item-status-message.error{background-color:var(--status-error-color);color:#fff;border:1px solid #a71d2a}
    .placeholder-message{padding:25px;text-align:center;color:var(--text-green-secondary);font-size:1.1em}
    .placeholder-message.error p{color:#ff5555;font-weight:bold}

    /* Modal Styling */
    .modal{
        display:none;
        position:fixed;
        z-index:1000;
        left:0;
        top:0;
        width:100%;
        height:100%;
        overflow:auto; /* Allow scrolling for the modal overlay itself if content overflows, though modal-content should prevent it */
        background-color:rgba(0,0,0,.6);
        font-family:var(--font-terminal);
    }
    .modal-content{
        background-color: var(--bg-terminal);
        color: var(--text-green-ai);
        margin: auto; /* Center the modal */
        padding: 20px; /* Padding around the header and body */
        border: 1px solid var(--border-line);
        width: 90vw; /* Modal takes 90% of viewport width */
        max-width: 1600px; /* But not more than 1600px wide */
        height: 90vh; /* Modal takes 90% of viewport height */
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,255,0,.2);
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Prevent modal-content itself from showing scrollbars */
    }
    .modal-header{
        padding: 10px 15px;
        border-bottom: 1px solid var(--border-line);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0; /* Prevent header from shrinking if body content is large */
    }
    .modal-header h2{
        margin:0;
        color:var(--text-green-ai);
        font-size:1.5em;
    }
    .close-button{
        color:var(--text-green-secondary);
        float:right; /* Not strictly necessary with flex but good fallback */
        font-size:28px;
        font-weight:bold;
        transition:color .2s;
    }
    .close-button:focus,
    .close-button:hover{
        color:red;
        text-decoration:none;
        cursor:pointer;
    }
    .modal-body{
        padding:0; /* Crucial: no padding here for iframe to fill edge-to-edge */
        flex-grow:1; /* Take remaining vertical space */
        overflow:hidden; /* Let iframe handle its own scrolling */
        display: flex; /* Make it a flex container for the iframe */
        flex-direction: column; /* Align children (iframe) vertically */
    }
    .modal-body iframe {
        width: 100%;
        height: 100%;
        border: none; /* Remove default iframe border */
        flex-grow: 1; /* Ensure iframe takes all available space in flex container */
    }
    .modal-body .pdf-error-message {
        padding: 20px;
        text-align: center;
        color: var(--status-error-color);
        overflow-y: auto; /* If error message is long */
    }
    .modal-body .pdf-error-message a {
        color: var(--text-green-user);
        text-decoration: underline;
    }
    .modal-body .pdf-error-message a:hover {
        color: var(--text-green-ai);
    }

</style>
</head>
<body>
    <div class="page-container">
        @include('auth.sidebar')

        <main class="main-content">
            <h1 class="content-header">KNOWLEDGE_PDF_SOURCES</h1>
            <div class="actions-bar">
                <button id="uploadPdfBtn" class="action-button">UPLOAD_NEW_PDF</button>
                <button id="refreshListBtn" class="action-button">REFRESH_LIST</button>
            </div>
            <div id="pdfListContainer" class="list-container">
                {{-- Initial placeholder will be managed by JS --}}
            </div>
        </main>
    </div>

    {{-- Modal for displaying PDF content --}}
    <div id="pdfContentViewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">PDF Viewer</h2>
                <span class="close-button" id="closeModalBtn">×</span>
            </div>
            <div class="modal-body" id="modalBodyContent">
                {{-- Content (iframe or error message) will be injected here by JS --}}
            </div>
        </div>
    </div>


    <template id="pdfItemTemplate">
        <div class="list-item" data-source-identifier="" >
            <span class="filename-link" title="View PDF Content"></span>
            <span class="details"></span>
            <div class="controls-container">
                <select class="role-multiselect" multiple>
                    {{-- Options will be populated by JS --}}
                </select>
                <button class="save-button" style="display: none;">SAVE</button>
                <button class="delete-button">DELETE</button>
                <span class="item-status-message"></span>
            </div>
        </div>
    </template>


    <script>
        const pdfListContainer = document.getElementById('pdfListContainer');
        const uploadPdfBtn = document.getElementById('uploadPdfBtn');
        const refreshListBtn = document.getElementById('refreshListBtn');
        const pdfItemTemplate = document.getElementById('pdfItemTemplate');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const PYTHON_API_URL_BASE = "{{ e(isset($python_api) ? $python_api : 'http://localhost:8001') }}";
        const LARAVEL_ROLES_LIST_URL   = "{{ route('knowledge.roles.list') }}";

        const pdfContentViewModal = document.getElementById('pdfContentViewModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalTitle = document.getElementById('modalTitle');
        const modalBodyContent = document.getElementById('modalBodyContent');

        function openModal() { if (pdfContentViewModal) pdfContentViewModal.style.display = 'block'; }
        function closeModal() {
            if (pdfContentViewModal) pdfContentViewModal.style.display = 'none';
            if (modalBodyContent) modalBodyContent.innerHTML = ''; 
            if (modalTitle) modalTitle.textContent = 'PDF Viewer';
        }
        if (closeModalBtn) closeModalBtn.onclick = closeModal;
        window.onclick = function(event) { if (event.target == pdfContentViewModal) closeModal(); }
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape" && pdfContentViewModal && pdfContentViewModal.style.display === 'block') closeModal();
        });

        function displayStatus(message, isError = false) {
            pdfListContainer.innerHTML = '';
            const statusDiv = document.createElement('div');
            statusDiv.classList.add('placeholder-message');
            if (isError) statusDiv.classList.add('error');
            const pElement = document.createElement('p');
            pElement.textContent = message;
            statusDiv.appendChild(pElement);
            pdfListContainer.appendChild(statusDiv);
        }
        const getSelectedRoleIds = (selectElement) => Array.from(selectElement.selectedOptions).map(opt => opt.value);
        const getSelectedRoleNames = (selectElement, allAvailableRoles) => {
            const selectedIds = getSelectedRoleIds(selectElement);
            return selectedIds.map(id => {
                const role = allAvailableRoles.find(r => r.id.toString() === id);
                return role ? role.name.toLowerCase() : null;
            }).filter(name => name !== null);
        };
        function showItemStatus(itemDiv, message, type = 'success', duration = 3000) {
            const statusSpan = itemDiv.querySelector('.item-status-message');
            if (!statusSpan) return;
            statusSpan.textContent = message;
            statusSpan.className = 'item-status-message';
            statusSpan.classList.add(type);
            statusSpan.style.display = 'inline-block';
            setTimeout(() => {
                if (statusSpan && statusSpan.isConnected) { statusSpan.style.display = 'none'; statusSpan.textContent = ''; }
            }, duration);
        }

        async function viewPdfContent(docName) {
            if (!docName) return;
            console.log(`View PDF initiated for: ${docName}`);
            if (modalTitle) modalTitle.textContent = `PDF: ${docName}`;
            if (modalBodyContent) {
                modalBodyContent.innerHTML = '<p style="padding: 20px; text-align:center;">LOADING_PDF_DOCUMENT...</p>';
            }
            openModal();

            const pdfFileUrl = `${PYTHON_API_URL_BASE}/pdf/${encodeURIComponent(docName)}/content`;
            console.log("Attempting to load PDF into iframe from:", pdfFileUrl);

            const iframe = document.createElement('iframe');
            iframe.src = pdfFileUrl;
            iframe.title = `PDF View for ${docName}`;
            // CSS will handle styling (width, height, border) via .modal-body iframe

            if (modalBodyContent) modalBodyContent.innerHTML = '';
            modalBodyContent.appendChild(iframe);

            iframe.onload = () => {
                console.log(`Iframe for ${docName} onload event fired.`);
            };

            iframe.onerror = (e) => {
                console.error(`Error event on iframe for ${docName}:`, e);
                if (modalBodyContent && modalBodyContent.contains(iframe)) { 
                    modalBodyContent.innerHTML = ''; 
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'pdf-error-message';
                    errorDiv.innerHTML = `
                        <p>ERROR_LOADING_PDF: Could not display PDF "${docName}".</p>
                        <p>The browser's PDF viewer might have encountered an issue, the file may not be accessible, or the server endpoint is not serving a viewable PDF.</p>
                        <p>Ensure the server at <a href="${pdfFileUrl}" target="_blank" rel="noopener noreferrer">${pdfFileUrl}</a> is serving the PDF file with 'Content-Type: application/pdf'.</p>
                        <p>You can also try to <a href="${pdfFileUrl}" target="_blank" rel="noopener noreferrer" download="${docName}.pdf">download "${docName}.pdf" directly</a>.</p>
                    `;
                    modalBodyContent.appendChild(errorDiv);
                }
            };
        }

        function renderPdfListWithTemplate(externalPdfs, availableRoles) {
            pdfListContainer.innerHTML = '';
            if (!pdfItemTemplate) { console.error("Template missing: pdfItemTemplate"); displayStatus("CLIENT_ERROR: UI template is missing.", true); return; }
            if (!externalPdfs || externalPdfs.length === 0) { displayStatus("NO_KNOWLEDGE_SOURCES_FOUND_FROM_EXTERNAL_API."); return; }
            if (!availableRoles || availableRoles.length === 0) { displayStatus("NO_ROLES_CONFIGURED_IN_SYSTEM. PLEASE_ADD_ROLES_FIRST.", true); return; }

            externalPdfs.forEach(pdf => {
                const clone = pdfItemTemplate.content.cloneNode(true);
                const itemDiv = clone.querySelector('.list-item');
                const docName = pdf.source;
                itemDiv.dataset.sourceIdentifier = docName;

                const fileNameLink = itemDiv.querySelector('.filename-link');
                if (fileNameLink) {
                    fileNameLink.textContent = docName;
                    fileNameLink.title = `View content of ${docName}`;
                    fileNameLink.addEventListener('click', (e) => {
                        e.preventDefault();
                        viewPdfContent(docName);
                    });
                }

                const detailsSpan = itemDiv.querySelector('.details');
                detailsSpan.textContent = `Pages: ${pdf.max_pages || 'N/A'}, Size: ${pdf.size_mb || 'N/A'} MB`;

                const controlsContainer = itemDiv.querySelector('.controls-container');
                const roleSelect = controlsContainer.querySelector('.role-multiselect');
                roleSelect.innerHTML = '';

                availableRoles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name.toUpperCase();
                    roleSelect.appendChild(option);
                    option.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        this.selected = !this.selected;
                        const changeEvent = new Event('change', { bubbles: true });
                        roleSelect.dispatchEvent(changeEvent);
                    });
                });

                let assignedRoleIdsForThisPdf = [];
                for (const key in pdf) {
                    if (key.startsWith('is_') && pdf[key] === true) {
                        const roleNameFromFlag = key.substring(3).toLowerCase();
                        const matchingRole = availableRoles.find(r => r.name.toLowerCase() === roleNameFromFlag);
                        if (matchingRole) { assignedRoleIdsForThisPdf.push(matchingRole.id.toString()); }
                    }
                }
                assignedRoleIdsForThisPdf = [...new Set(assignedRoleIdsForThisPdf)];
                Array.from(roleSelect.options).forEach(optionEl => { if (assignedRoleIdsForThisPdf.includes(optionEl.value)) optionEl.selected = true; });
                
                roleSelect.dataset.originalRoleIds = JSON.stringify(getSelectedRoleIds(roleSelect).sort());

                const saveBtn = controlsContainer.querySelector('.save-button');
                saveBtn.style.display = 'none';
                const itemStatusSpan = controlsContainer.querySelector('.item-status-message');
                const deleteBtn = controlsContainer.querySelector('.delete-button');

                roleSelect.addEventListener('change', () => {
                    const currentSelectedIdsStr = JSON.stringify(getSelectedRoleIds(roleSelect).sort());
                    saveBtn.style.display = (currentSelectedIdsStr !== roleSelect.dataset.originalRoleIds) ? 'inline-block' : 'none';
                    if (itemStatusSpan) itemStatusSpan.style.display = 'none';
                });

                saveBtn.addEventListener('click', async () => {
                    const docNameForSave = itemDiv.dataset.sourceIdentifier;
                    const selectedNewRoleNames = getSelectedRoleNames(roleSelect, availableRoles);
                    saveBtn.textContent = 'SAVING...'; saveBtn.disabled = true; if (itemStatusSpan) itemStatusSpan.style.display = 'none';
                    const payload = { doc_name: docNameForSave, new_role: selectedNewRoleNames };
                    try {
                        const pythonUpdateUrl = `${PYTHON_API_URL_BASE}/update_role`;
                        const response = await fetch(pythonUpdateUrl, { method: 'PUT', headers: {'Content-Type':'application/json','Accept':'application/json', 'X-CSRF-TOKEN': csrfToken}, body: JSON.stringify(payload) });
                        let result; const contentType = response.headers.get("content-type");
                        if (contentType && contentType.includes("application/json")) { result = await response.json(); }
                        else { result = { message: await response.text() }; if (!response.ok) { result.error = result.message; } }
                        
                        if (!response.ok) { let errorMsg = result.message || result.error || result.detail || "PYTHON_API_SAVE_FAILED"; showItemStatus(itemDiv, `ERR: ${errorMsg}`, 'error', 7000); throw new Error(errorMsg); }
                        
                        showItemStatus(itemDiv, result.message || 'SAVED_TO_PY_API!', 'success');
                        roleSelect.dataset.originalRoleIds = JSON.stringify(getSelectedRoleIds(roleSelect).sort());
                        
                        const currentPdfData = externalPdfs.find(p => p.source === docNameForSave);
                        if (currentPdfData) { 
                            availableRoles.forEach(laravelRole => { 
                                const flagName = `is_${laravelRole.name.toLowerCase()}`; 
                                currentPdfData[flagName] = selectedNewRoleNames.includes(laravelRole.name.toLowerCase()); 
                            }); 
                        }
                        saveBtn.style.display = 'none';
                    } catch (error) { 
                        console.error("Save error:", error);
                        if (itemDiv && itemDiv.isConnected) { showItemStatus(itemDiv, error.message || 'PY_API_SAVE_ERR', 'error', 7000); }
                    } finally { 
                        if (saveBtn && saveBtn.isConnected) { saveBtn.textContent = 'SAVE'; saveBtn.disabled = false; } 
                    }
                });

                if (deleteBtn) {
                    deleteBtn.addEventListener('click', async () => {
                        const docNameToDelete = itemDiv.dataset.sourceIdentifier;
                        if (!confirm(`SYS_CONFIRM: Are you sure you want to delete "${docNameToDelete}"? This action cannot be undone.`)) return;

                        deleteBtn.textContent = 'DELETING...'; deleteBtn.disabled = true; if (itemStatusSpan) itemStatusSpan.style.display = 'none';

                        try {
                            const deleteUrl = `${PYTHON_API_URL_BASE}/pdf/${encodeURIComponent(docNameToDelete)}`;
                            const response = await fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                            });

                            let resultMessage; let success = response.ok;
                            try {
                                if (response.headers.get("content-type")?.includes("application/json")) {
                                    const result = await response.json();
                                    resultMessage = result.message || result.detail || (success ? "DELETED_SUCCESSFULLY" : "DELETE_FAILED");
                                } else {
                                    const textResponse = await response.text();
                                    resultMessage = textResponse || (success ? "DELETED_SUCCESSFULLY" : "DELETE_FAILED_CHECK_SERVER");
                                }
                            } catch (parseError) {
                                resultMessage = success ? "DELETED_SUCCESSFULLY_NO_JSON" : "DELETE_FAILED_INVALID_RESPONSE";
                                console.warn("Error parsing delete response:", parseError);
                            }
                            

                            if (!success) {
                                showItemStatus(itemDiv, `ERR: ${resultMessage}`, 'error', 7000);
                                throw new Error(resultMessage);
                            }

                            showItemStatus(itemDiv, resultMessage, 'success', 4000);
                            itemDiv.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out, height 0.5s ease-out, padding 0.5s ease-out, margin 0.5s ease-out';
                            itemDiv.style.opacity = '0';
                            itemDiv.style.transform = 'scaleY(0)';
                            itemDiv.style.height = '0';
                            itemDiv.style.paddingTop = '0'; itemDiv.style.paddingBottom = '0';
                            itemDiv.style.marginTop = '0'; itemDiv.style.marginBottom = '0';
                            setTimeout(() => {
                                itemDiv.remove();
                                if (pdfListContainer.children.length === 0) {
                                    displayStatus("NO_KNOWLEDGE_SOURCES_REMAINING.");
                                }
                            }, 500);

                        } catch (error) {
                            console.error("Failed to delete PDF:", error.message);
                            if (itemDiv && itemDiv.isConnected) {
                                showItemStatus(itemDiv, error.message || 'PY_API_DELETE_ERR', 'error', 7000);
                            }
                        } finally {
                            if (deleteBtn && deleteBtn.isConnected) {
                                deleteBtn.textContent = 'DELETE';
                                deleteBtn.disabled = false;
                            }
                        }
                    });
                }
                pdfListContainer.appendChild(itemDiv);
            });
        }

        async function fetchAllDataAndRender() {
            displayStatus("ACCESSING_KNOWLEDGE_SYSTEMS...");
            try {
                const [externalPdfResponse, rolesResponse] = await Promise.all([
                    fetch(`${PYTHON_API_URL_BASE}/pdf`, { headers: { 'Accept': 'application/json' } }),
                    fetch(LARAVEL_ROLES_LIST_URL, { headers: { 'Accept': 'application/json' } })
                ]);
                const processResponse = async (response, apiName) => {
                    if (!response.ok) {
                        let errorDetail = `Status: ${response.status} ${response.statusText}`;
                        try { const errorData = await response.json(); errorDetail = errorData.message || errorData.error || errorData.detail || errorDetail; } catch (e) {/* ignore */}
                        throw new Error(`${apiName}_API_ERR: ${errorDetail}`);
                    }
                    return response.json();
                };
                const externalPdfs = await processResponse(externalPdfResponse, "PYTHON_PDF_LIST");
                if (!Array.isArray(externalPdfs)) throw new Error("PYTHON_API_UNEXPECTED_DATA (expected array of PDFs)");
                
                const availableRoles = await processResponse(rolesResponse, "LARAVEL_ROLES");
                if (!Array.isArray(availableRoles)) throw new Error("LARAVEL_ROLES_API_UNEXPECTED_DATA (expected array of roles)");
                
                renderPdfListWithTemplate(externalPdfs, availableRoles);
            } catch (error) {
                console.error("Failed to fetch initial data:", error);
                displayStatus(`DATA_FETCH_ERR: ${error.message}. CHECK_CONSOLE_AND_NETWORK_TAB.`, true);
            }
        }

        uploadPdfBtn.addEventListener('click', () => {
            const uploadUrl = `${PYTHON_API_URL_BASE}/pdf/upload`;
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = ".pdf,application/pdf"; // More robust accept attribute
            fileInput.style.display = 'none';
            document.body.appendChild(fileInput);
            
            fileInput.addEventListener('change', async () => {
                if (fileInput.files.length > 0) {
                    const selectedFile = fileInput.files[0];
                    const MAX_FILE_SIZE_BYTES = 10 * 1024 * 1024; // 10MB
                    // Check file type more reliably, considering .pdf extension as fallback
                    if (!selectedFile.type.startsWith("application/pdf") && !selectedFile.name.toLowerCase().endsWith(".pdf")) {
                        alert(`Invalid file type. Please select a PDF file.\nSelected type: ${selectedFile.type || 'unknown'}, name: ${selectedFile.name}`);
                        if(fileInput.isConnected) document.body.removeChild(fileInput); return;
                    }
                    if (selectedFile.size > MAX_FILE_SIZE_BYTES) {
                        alert(`File is too large. Max size is ${MAX_FILE_SIZE_BYTES / (1024*1024)}MB.\nYour file is ${(selectedFile.size / (1024*1024)).toFixed(2)}MB.`);
                        if(fileInput.isConnected) document.body.removeChild(fileInput); return;
                    }
                    
                    const formData = new FormData();
                    formData.append('file', selectedFile);
                    uploadPdfBtn.disabled = true; uploadPdfBtn.textContent = 'UPLOADING...';
                    
                    try {
                        const response = await fetch(uploadUrl, { 
                            method: 'POST', 
                            body: formData,
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                        });
                        
                        let responseData = {}; let responseText = '';
                        try { 
                            responseText = await response.text();
                            responseData = JSON.parse(responseText); 
                        } catch (e) { 
                            responseData.message = responseText || "Upload processed, but response was not valid JSON.";
                            if (!response.ok) responseData.error = responseData.message;
                            console.warn("Upload response parse error or not JSON:", e, responseText);
                        }

                        if (response.ok) {
                            alert('PDF uploaded successfully: ' + (responseData.message || JSON.stringify(responseData)));
                            fetchAllDataAndRender();
                        } else {
                            let errorMsg = `Upload failed. Server responded with ${response.status}: ${response.statusText}`;
                            errorMsg += `\nDetails: ${responseData.detail || responseData.error || responseData.message || JSON.stringify(responseData)}`;
                            alert(errorMsg);
                        }
                    } catch (error) { 
                        alert(`An error occurred during upload: ${error.message}`);
                        console.error("Upload error:", error);
                    } finally { 
                        uploadPdfBtn.disabled = false; 
                        uploadPdfBtn.textContent = 'UPLOAD_NEW_PDF'; 
                        if (fileInput.isConnected) document.body.removeChild(fileInput); 
                    }
                } else { 
                    if (fileInput.isConnected) document.body.removeChild(fileInput); 
                }
            });
            fileInput.click();
        });

        refreshListBtn.addEventListener('click', fetchAllDataAndRender);
        document.addEventListener('DOMContentLoaded', fetchAllDataAndRender);
    </script>
</body>
</html>