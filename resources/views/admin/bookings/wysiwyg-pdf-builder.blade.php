@extends('layouts.admin')

@section('content')
<div class="container-fluid p-0" style="height: 100vh; display: flex; flex-direction: column;">
    <!-- Header -->
    <div class="bg-dark text-white p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">PDF Template WYSIWYG Builder</h4>
                @if($booking ?? false)
                    <small>Booking #{{ $booking->id }} - {{ $booking->booking_reference }}</small>
                @endif
            </div>
            <div>
                <button class="btn btn-success btn-sm me-2" onclick="generatePDFFromBuilder()">
                    <i class="fas fa-download"></i> Generate PDF
                </button>
                @if($booking ?? false)
                    <button class="btn btn-info btn-sm me-2" onclick="saveTemplateWithBooking()">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                @else
                    <button class="btn btn-info btn-sm me-2" onclick="saveTemplate()">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                @endif
                <button class="btn btn-warning btn-sm" onclick="loadTemplate()">
                    <i class="fas fa-folder-open"></i> Load Template
                </button>
            </div>
        </div>
    </div>

    <div class="d-flex flex-grow-1" style="overflow: hidden;">
        <!-- Left Sidebar - Component Library -->
        <div class="bg-light p-3" style="width: 250px; overflow-y: auto; border-right: 1px solid #ddd;">
            <h6 class="mb-3">Components</h6>
            
            <div class="list-group list-group-sm mb-3">
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'heading')">
                    <i class="fas fa-heading"></i> Heading
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'paragraph')">
                    <i class="fas fa-align-left"></i> Paragraph
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'image')">
                    <i class="fas fa-image"></i> Image
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'table')">
                    <i class="fas fa-table"></i> Table
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'divider')">
                    <i class="fas fa-minus"></i> Divider
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'spacer')">
                    <i class="fas fa-arrows-alt-v"></i> Spacer
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'barcode')">
                    <i class="fas fa-barcode"></i> Barcode
                </button>
                <button class="list-group-item list-group-item-action" draggable="true" ondragstart="dragStart(event, 'qrcode')">
                    <i class="fas fa-qrcode"></i> QR Code
                </button>
            </div>

            <h6 class="mb-3 mt-4">Variables</h6>
            <div class="list-group list-group-sm">
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('booking_reference')">
                    @{{booking_reference}}
                </button>
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('customer_name')">
                    @{{customer_name}}
                </button>
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('customer_email')">
                    @{{customer_email}}
                </button>
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('customer_phone')">
                    @{{customer_phone}}
                </button>
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('booking_date')">
                    @{{booking_date}}
                </button>
                <button class="list-group-item list-group-item-action p-2" onclick="insertVariable('airline_pnr')">
                    @{{airline_pnr}}
                </button>
            </div>

            <hr>

            <h6 class="mb-3">Presets</h6>
            <button class="btn btn-secondary btn-sm w-100 mb-2" onclick="loadPreset('airline-ticket')">
                Airline Ticket
            </button>
            <button class="btn btn-secondary btn-sm w-100 mb-2" onclick="loadPreset('hotel-voucher')">
                Hotel Voucher
            </button>
            <button class="btn btn-secondary btn-sm w-100" onclick="loadPreset('blank')">
                Blank Template
            </button>
        </div>

        <!-- Center - Editor Canvas -->
        <div class="flex-grow-1 p-4" style="overflow-y: auto; background: #f5f5f5;">
            <div id="builder-canvas" 
                 class="bg-white mx-auto p-8" 
                 ondrop="drop(event)" 
                 ondragover="dragOver(event)" 
                 style="width: 595px; min-height: 842px; box-shadow: 0 0 20px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <p class="text-muted text-center py-5">Drag components here to build your template</p>
            </div>
        </div>

        <!-- Right Sidebar - Inspector -->
        <div class="bg-light p-3" style="width: 300px; overflow-y: auto; border-left: 1px solid #ddd;">
            <h6 class="mb-3">Inspector</h6>
            
            <div id="inspector-panel">
                <p class="text-muted">Select a component to edit</p>
            </div>

            <hr class="mt-4">

            <h6 class="mb-3">Page Settings</h6>
            <div class="mb-3">
                <label class="form-label">Page Size</label>
                <select class="form-select form-select-sm" id="pageSize" onchange="changePageSize()">
                    <option value="a4">A4 (210×297mm)</option>
                    <option value="letter">Letter (8.5×11in)</option>
                    <option value="a5">A5 (148×210mm)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Margins (px)</label>
                <input type="number" class="form-control form-control-sm" id="pageMargin" value="20" onchange="updatePageSettings()">
            </div>

            <div class="mb-3">
                <label class="form-label">Background Color</label>
                <input type="color" class="form-control form-control-sm" id="bgColor" value="#ffffff" onchange="updatePageSettings()">
            </div>

            <hr>

            <h6 class="mb-3">Layers</h6>
            <div id="layers-list" class="list-group list-group-sm">
                <p class="text-muted">No components</p>
            </div>

            <hr>

            <button class="btn btn-danger btn-sm w-100" id="deleteBtn" style="display: none;" onclick="deleteSelected()">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>

<!-- Hidden Inputs -->
<input type="file" id="imageInput" style="display: none;" accept="image/*" onchange="handleImageUpload()">
<input type="file" id="templateInput" style="display: none;" accept=".json" onchange="handleTemplateUpload()">

<style>
    body, html {
        margin: 0;
        padding: 0;
    }

    #builder-canvas {
        position: relative;
        cursor: pointer;
    }

    .builder-component {
        position: relative;
        border: 2px dashed #ccc;
        padding: 10px;
        margin: 10px 0;
        cursor: move;
        transition: all 0.2s;
        user-select: none;
    }

    .builder-component:hover {
        border-color: #007bff;
        background: rgba(0, 123, 255, 0.05);
    }

    .builder-component.selected {
        border-color: #007bff;
        background: rgba(0, 123, 255, 0.1);
    }

    .component-handle {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        padding: 5px 8px;
        background: #007bff;
        color: white;
        font-size: 12px;
        font-weight: bold;
        cursor: move;
        display: none;
    }

    .builder-component.selected .component-handle {
        display: block;
    }

    .component-delete {
        position: absolute;
        top: 0;
        right: 0;
        background: #dc3545;
        color: white;
        border: none;
        padding: 2px 6px;
        cursor: pointer;
        display: none;
        font-size: 10px;
    }

    .builder-component.selected .component-delete {
        display: block;
    }

    .drag-over {
        background: rgba(0, 123, 255, 0.1) !important;
        border: 2px dashed #007bff !important;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    const canvas = document.getElementById('builder-canvas');
    let components = [];
    let selectedComponent = null;
    let componentCounter = 0;
    const pageSizes = {
        a4: 595,
        letter: 612,
        a5: 420
    };

    // Drag start
    function dragStart(e, type) {
        e.dataTransfer.effectAllowed = 'copy';
        e.dataTransfer.setData('componentType', type);
    }

    // Drag over
    function dragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        canvas.classList.add('drag-over');
    }

    // Drop
    function drop(e) {
        e.preventDefault();
        canvas.classList.remove('drag-over');
        const type = e.dataTransfer.getData('componentType');
        if (type) {
            addComponent(type);
        }
    }

    // Add component
    function addComponent(type) {
        const id = ++componentCounter;
        const component = {
            id: id,
            type: type,
            properties: getDefaultProperties(type)
        };
        components.push(component);
        renderComponent(component);
        updateLayersList();
    }

    // Get default properties
    function getDefaultProperties(type) {
        const defaults = {
            heading: {
                text: 'Your Heading Here',
                fontSize: 24,
                fontWeight: 'bold',
                textAlign: 'left',
                color: '#000000'
            },
            paragraph: {
                text: 'Your text content here',
                fontSize: 12,
                textAlign: 'left',
                color: '#000000',
                lineHeight: 1.5
            },
            image: {
                src: '',
                width: 200,
                height: 150,
                align: 'left'
            },
            table: {
                rows: 3,
                cols: 3,
                borderColor: '#000000',
                headerBg: '#f0f0f0'
            },
            divider: {
                color: '#cccccc',
                height: 2,
                margin: 10
            },
            spacer: {
                height: 20
            },
            barcode: {
                value: '123456789',
                height: 50
            },
            qrcode: {
                value: 'https://example.com',
                size: 100
            }
        };
        return defaults[type] || {};
    }

    // Render component
    function renderComponent(component) {
        const el = document.createElement('div');
        el.className = 'builder-component';
        el.id = `component-${component.id}`;
        el.onclick = () => selectComponent(component);
        el.draggable = true;
        el.ondragstart = (e) => e.dataTransfer.setData('componentId', component.id);

        const handle = document.createElement('div');
        handle.className = 'component-handle';
        handle.textContent = component.type.toUpperCase() + ' #' + component.id;
        el.appendChild(handle);

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'component-delete';
        deleteBtn.textContent = '✕';
        deleteBtn.onclick = (e) => { deleteComponent(component.id); e.stopPropagation(); };
        el.appendChild(deleteBtn);

        let content = '';
        switch (component.type) {
            case 'heading':
                content = `<h3 style="font-size: ${component.properties.fontSize}px; font-weight: ${component.properties.fontWeight}; color: ${component.properties.color}; text-align: ${component.properties.textAlign};">${component.properties.text}</h3>`;
                break;
            case 'paragraph':
                content = `<p style="font-size: ${component.properties.fontSize}px; color: ${component.properties.color}; text-align: ${component.properties.textAlign}; line-height: ${component.properties.lineHeight};">${component.properties.text}</p>`;
                break;
            case 'image':
                content = `<img src="${component.properties.src}" style="width: ${component.properties.width}px; height: ${component.properties.height}px;">`;
                break;
            case 'table':
                const rows = component.properties.rows || 3;
                const cols = component.properties.cols || 3;
                let table = '<table style="width: 100%; border-collapse: collapse;">';
                for (let i = 0; i < rows; i++) {
                    table += '<tr>';
                    for (let j = 0; j < cols; j++) {
                        const isHeader = i === 0;
                        table += `<td style="border: 1px solid ${component.properties.borderColor}; padding: 8px; background: ${isHeader ? component.properties.headerBg : 'white'};">Cell</td>`;
                    }
                    table += '</tr>';
                }
                table += '</table>';
                content = table;
                break;
            case 'divider':
                content = `<hr style="border: none; border-top: ${component.properties.height}px solid ${component.properties.color}; margin: ${component.properties.margin}px 0;">`;
                break;
            case 'spacer':
                content = `<div style="height: ${component.properties.height}px;"></div>`;
                break;
            case 'barcode':
                content = `<svg id="barcode-${component.id}" style="width: 100%; height: ${component.properties.height}px;"></svg>`;
                break;
            case 'qrcode':
                content = `<div id="qrcode-${component.id}" style="width: ${component.properties.size}px; height: ${component.properties.size}px; margin: 10px 0;"></div>`;
                break;
        }

        el.innerHTML += content;
        canvas.appendChild(el);

        // Generate barcode/QR if needed
        if (component.type === 'barcode') {
            setTimeout(() => {
                try {
                    JsBarcode(`#barcode-${component.id}`, component.properties.value);
                } catch (e) {
                    console.log('Barcode error:', e);
                }
            }, 100);
        } else if (component.type === 'qrcode') {
            setTimeout(() => {
                new QRCode(document.getElementById(`qrcode-${component.id}`), {
                    text: component.properties.value,
                    width: component.properties.size,
                    height: component.properties.size,
                });
            }, 100);
        }
    }

    // Select component
    function selectComponent(component) {
        if (selectedComponent) {
            document.getElementById(`component-${selectedComponent.id}`)?.classList.remove('selected');
        }
        selectedComponent = component;
        document.getElementById(`component-${component.id}`).classList.add('selected');
        updateInspectorPanel();
        updateLayersList();
        document.getElementById('deleteBtn').style.display = 'block';
    }

    // Update inspector panel
    function updateInspectorPanel() {
        const panel = document.getElementById('inspector-panel');
        if (!selectedComponent) {
            panel.innerHTML = '<p class="text-muted">Select a component to edit</p>';
            return;
        }

        let html = `<div class="mb-3"><label class="form-label">Component Type</label><input type="text" class="form-control form-control-sm" value="${selectedComponent.type}" disabled></div>`;

        const props = selectedComponent.properties;
        if (selectedComponent.type === 'heading' || selectedComponent.type === 'paragraph') {
            html += `
                <div class="mb-3">
                    <label class="form-label">Text</label>
                    <textarea class="form-control form-control-sm" onchange="selectedComponent.properties.text = this.value; rerender();">${props.text}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Font Size</label>
                    <input type="number" class="form-control form-control-sm" value="${props.fontSize}" onchange="selectedComponent.properties.fontSize = parseInt(this.value); rerender();">
                </div>
                <div class="mb-3">
                    <label class="form-label">Text Color</label>
                    <input type="color" class="form-control form-control-sm" value="${props.color}" onchange="selectedComponent.properties.color = this.value; rerender();">
                </div>
                <div class="mb-3">
                    <label class="form-label">Align</label>
                    <select class="form-select form-select-sm" onchange="selectedComponent.properties.textAlign = this.value; rerender();">
                        <option value="left" ${props.textAlign === 'left' ? 'selected' : ''}>Left</option>
                        <option value="center" ${props.textAlign === 'center' ? 'selected' : ''}>Center</option>
                        <option value="right" ${props.textAlign === 'right' ? 'selected' : ''}>Right</option>
                    </select>
                </div>
            `;
        } else if (selectedComponent.type === 'image') {
            html += `
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <button class="btn btn-sm btn-secondary w-100" onclick="selectImageForComponent()">Choose Image</button>
                </div>
                <div class="mb-3">
                    <label class="form-label">Width (px)</label>
                    <input type="number" class="form-control form-control-sm" value="${props.width}" onchange="selectedComponent.properties.width = parseInt(this.value); rerender();">
                </div>
                <div class="mb-3">
                    <label class="form-label">Height (px)</label>
                    <input type="number" class="form-control form-control-sm" value="${props.height}" onchange="selectedComponent.properties.height = parseInt(this.value); rerender();">
                </div>
            `;
        } else if (selectedComponent.type === 'barcode') {
            html += `
                <div class="mb-3">
                    <label class="form-label">Value</label>
                    <input type="text" class="form-control form-control-sm" value="${props.value}" onchange="selectedComponent.properties.value = this.value; rerender();">
                </div>
            `;
        } else if (selectedComponent.type === 'qrcode') {
            html += `
                <div class="mb-3">
                    <label class="form-label">URL/Text</label>
                    <input type="text" class="form-control form-control-sm" value="${props.value}" onchange="selectedComponent.properties.value = this.value; rerender();">
                </div>
                <div class="mb-3">
                    <label class="form-label">Size (px)</label>
                    <input type="number" class="form-control form-control-sm" value="${props.size}" onchange="selectedComponent.properties.size = parseInt(this.value); rerender();">
                </div>
            `;
        }

        panel.innerHTML = html;
    }

    // Update layers list
    function updateLayersList() {
        const list = document.getElementById('layers-list');
        if (components.length === 0) {
            list.innerHTML = '<p class="text-muted">No components</p>';
            return;
        }

        list.innerHTML = components.map(comp => `
            <div class="list-group-item p-2 ${selectedComponent?.id === comp.id ? 'active' : ''}" onclick="selectComponent(components[${components.indexOf(comp)}])" style="cursor: pointer;">
                <small>${comp.type} #${comp.id}</small>
            </div>
        `).join('');
    }

    // Delete component
    function deleteComponent(id) {
        components = components.filter(c => c.id !== id);
        document.getElementById(`component-${id}`)?.remove();
        selectedComponent = null;
        updateInspectorPanel();
        updateLayersList();
        document.getElementById('deleteBtn').style.display = 'none';
    }

    function deleteSelected() {
        if (selectedComponent) {
            deleteComponent(selectedComponent.id);
        }
    }

    // Rerender component
    function rerender() {
        if (!selectedComponent) return;
        document.getElementById(`component-${selectedComponent.id}`)?.remove();
        renderComponent(selectedComponent);
    }

    // Insert variable
    function insertVariable(varName) {
        if (selectedComponent && (selectedComponent.type === 'heading' || selectedComponent.type === 'paragraph')) {
            selectedComponent.properties.text += ' {{' + varName + '}}';
            rerender();
            updateInspectorPanel();
        }
    }

    // Select image for component
    function selectImageForComponent() {
        document.getElementById('imageInput').click();
    }

    function handleImageUpload() {
        const file = document.getElementById('imageInput').files[0];
        if (!file || !selectedComponent || selectedComponent.type !== 'image') return;

        const reader = new FileReader();
        reader.onload = (e) => {
            selectedComponent.properties.src = e.target.result;
            rerender();
        };
        reader.readAsDataURL(file);
    }

    // Change page size
    function changePageSize() {
        const size = document.getElementById('pageSize').value;
        canvas.style.width = (pageSizes[size] || 595) + 'px';
    }

    // Update page settings
    function updatePageSettings() {
        const margin = document.getElementById('pageMargin').value;
        const bgColor = document.getElementById('bgColor').value;
        canvas.style.margin = margin + 'px';
        canvas.style.backgroundColor = bgColor;
    }

    // Generate PDF from builder
    async function generatePDFFromBuilder() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'portrait',
            unit: 'px',
            format: [595, 842]
        });

        const canvas = document.getElementById('builder-canvas');
        const img = await html2canvas(canvas, { scale: 2 });
        doc.addImage(img.toDataURL('image/png'), 'PNG', 0, 0, 595, 842);
        doc.save('template-output.pdf');
    }

    // Save template
    function saveTemplate() {
        const templateData = {
            components: components,
            timestamp: new Date().toISOString()
        };
        const json = JSON.stringify(templateData, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `template-${Date.now()}.json`;
        a.click();
    }

    // Load template
    function loadTemplate() {
        document.getElementById('templateInput').click();
    }

    function handleTemplateUpload() {
        const file = document.getElementById('templateInput').files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const data = JSON.parse(e.target.result);
            components = data.components;
            componentCounter = Math.max(...components.map(c => c.id), 0);
            canvas.innerHTML = '';
            components.forEach(comp => renderComponent(comp));
            updateLayersList();
        };
        reader.readAsText(file);
    }

    // Load presets
    function loadPreset(presetName) {
        components = [];
        canvas.innerHTML = '';
        componentCounter = 0;

        if (presetName === 'airline-ticket') {
            addComponent('heading');
            components[componentCounter - 1].properties = { text: 'AIRLINE TICKET', fontSize: 28, fontWeight: 'bold', textAlign: 'center', color: '#000' };
            
            addComponent('divider');
            
            addComponent('paragraph');
            components[componentCounter - 1].properties = { text: 'Passenger: @{{customer_name}}', fontSize: 12, textAlign: 'left', color: '#000', lineHeight: 1.5 };
            
            addComponent('paragraph');
            components[componentCounter - 1].properties = { text: 'PNR: @{{airline_pnr}}', fontSize: 12, textAlign: 'left', color: '#000', lineHeight: 1.5 };
            
            addComponent('barcode');
            components[componentCounter - 1].properties = { value: '123456789', height: 50 };
            
            components.forEach(comp => renderComponent(comp));
        } else if (presetName === 'hotel-voucher') {
            addComponent('heading');
            components[componentCounter - 1].properties = { text: 'HOTEL VOUCHER', fontSize: 28, fontWeight: 'bold', textAlign: 'center', color: '#000' };
            
            addComponent('paragraph');
            components[componentCounter - 1].properties = { text: 'Guest: @{{customer_name}}', fontSize: 12, textAlign: 'left', color: '#000' };
            
            components.forEach(comp => renderComponent(comp));
        }
        updateLayersList();
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        updatePageSettings();
        
        // Load booking template if booking ID is available
        @if($booking ?? false)
            loadBookingTemplate({{ $booking->id }});
        @endif
    });

    // Load booking template with actual values
    function loadBookingTemplate(bookingId) {
        fetch(`/admin/bookings/${bookingId}/template-data`)
            .then(response => response.json())
            .then(data => {
                // Create default template with booking data
                components = [];
                canvas.innerHTML = '';
                componentCounter = 0;

                // Header
                addComponent('heading');
                components[componentCounter - 1].properties = {
                    text: data.booking.airline_name + ' e-Ticket',
                    fontSize: 28,
                    fontWeight: 'bold',
                    textAlign: 'center',
                    color: '#003366'
                };

                // Route Info
                addComponent('paragraph');
                components[componentCounter - 1].properties = {
                    text: data.booking.departure_city + ' → ' + data.booking.arrival_city + ' | ' + data.booking.departure_date,
                    fontSize: 14,
                    textAlign: 'left',
                    color: '#000'
                };

                // Booking Details Table
                addComponent('table');
                components[componentCounter - 1].properties = {
                    rows: 2,
                    cols: 3,
                    borderColor: '#ddd',
                    headerBg: '#f0f0f0'
                };

                // Segments
                data.segments.forEach((segment, index) => {
                    addComponent('paragraph');
                    components[componentCounter - 1].properties = {
                        text: segment.flight_number + ' | ' + segment.departure_time + ' → ' + segment.arrival_time,
                        fontSize: 12,
                        textAlign: 'left',
                        color: '#333'
                    };
                });

                // Passenger Table
                if (data.passengers.length > 0) {
                    addComponent('heading');
                    components[componentCounter - 1].properties = {
                        text: 'Passengers',
                        fontSize: 16,
                        fontWeight: 'bold',
                        textAlign: 'left',
                        color: '#003366'
                    };

                    addComponent('table');
                    components[componentCounter - 1].properties = {
                        rows: data.passengers.length + 1,
                        cols: 4,
                        borderColor: '#ddd',
                        headerBg: '#003366'
                    };
                }

                updateLayersList();
                components.forEach(comp => renderComponent(comp));
            })
            .catch(error => console.log('Note: No booking data available. Using blank template.', error));
    }

    // Save template with booking reference
    function saveTemplateWithBooking() {
        @if($booking ?? false)
            const templateData = {
                components: components,
                template_name: 'Ticket Template - Booking #' + {{ $booking->id }}
            };

            fetch(`/admin/bookings/{{ $booking->id }}/template-save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(templateData)
            })
            .then(response => response.json())
            .then(data => {
                alert('Template saved successfully!');
                console.log(data);
            })
            .catch(error => {
                alert('Error saving template');
                console.error(error);
            });
        @else
            saveTemplate();
        @endif
    }
</script>

@endsection
