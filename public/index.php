<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Capture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f8f9fb;
        }

        h1 {
            color: #2b4c7e;
            margin-bottom: 10px;
        }

        form {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        fieldset {
            border: none;
            margin-bottom: 20px;
        }

        legend {
            font-weight: bold;
            color: #2b4c7e;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #2b4c7e;
            color: #fff;
        }

        .item-row input, .item-row select {
            width: 100%;
            box-sizing: border-box;
        }

        .totals {
            text-align: right;
            margin-top: 20px;
        }

        .totals input {
            width: 150px;
            text-align: right;
        }

        button {
            background: #2b4c7e;
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #436db0;
        }
    </style>
</head>
<body>
<h1>Invoice Capture</h1>

<form id="invoiceForm">
    <!-- Invoice Details -->
    <fieldset>
        <legend>Invoice Details</legend>

        <label>Invoice Date</label>
        <input type="date" name="invoice_date" required>

        <label>Due Date</label>
        <input type="date" name="due_date">

        <label>Client</label>
        <select id="clientSelect" name="client_id" required>
            <option value="">-- Select Client --</option>
        </select>
    </fieldset>

    <!-- Invoice Items -->
    <fieldset>
        <legend>Invoice Items</legend>
        <table id="itemsTable">
            <thead>
            <tr>
                <th>Description</th>
                <th>Taxed?</th>
                <th>Amount</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="itemRows">
            <tr class="item-row">
                <td><input type="text" name="description[]" placeholder="e.g. Service Fee" required></td>
                <td style="text-align:center;"><input type="checkbox" name="taxed[]"></td>
                <td><input type="number" name="amount[]" step="0.01" required></td>
                <td><button type="button" class="removeRow">×</button></td>
            </tr>
            </tbody>
        </table>
        <button type="button" id="addItem">+ Add Item</button>
    </fieldset>

    <!-- Tax and Totals -->
    <fieldset>
        <legend>Totals</legend>
        <div class="totals">
            <label>Tax Rate (%)</label>
            <input type="number" name="tax_rate" value="15" step="0.01">

            <label>Subtotal</label>
            <input type="text" name="subtotal" readonly>

            <label>Tax Due</label>
            <input type="text" name="tax_due" readonly>

            <label>Total</label>
            <input type="text" name="total" readonly>
        </div>
    </fieldset>

    <!-- Comments -->
    <fieldset>
        <legend>Comments</legend>
        <textarea name="comments" rows="3" placeholder="Payment due in 30 days..."></textarea>
    </fieldset>

    <button type="submit">Save Invoice</button>
</form>

<script>
    const addItemBtn = document.getElementById('addItem');
    const itemRows = document.getElementById('itemRows');
    const form = document.getElementById('invoiceForm');
    const clientSelect = document.getElementById('clientSelect');

    // Load clients on page load
    async function loadClients() {
        try {
            const res = await fetch('/api/clients.php');
            const json = await res.json();

            if (json.success && json.data) {
                json.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;
                    clientSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Failed to load clients:', error);
        }
    }

    loadClients();

    // Add new line item
    addItemBtn.addEventListener('click', () => {
        const newRow = itemRows.querySelector('.item-row').cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => {
            input.value = '';
            if (input.type === 'checkbox') input.checked = false;
        });
        itemRows.appendChild(newRow);
    });

    // Remove item row
    document.addEventListener('click', e => {
        if (e.target.classList.contains('removeRow')) {
            const rows = itemRows.querySelectorAll('.item-row');
            if (rows.length > 1) e.target.closest('tr').remove();
        }
    });

    // Calculate totals dynamically
    form.addEventListener('input', () => {
        const taxRate = parseFloat(form.tax_rate.value) / 100;
        let subtotal = 0;
        let taxable = 0;

        const rows = itemRows.querySelectorAll('.item-row');
        rows.forEach(row => {
            const amount = parseFloat(row.querySelector('input[name="amount[]"]').value) || 0;
            const taxed = row.querySelector('input[name="taxed[]"]').checked;
            subtotal += amount;
            if (taxed) taxable += amount;
        });

        const taxDue = taxable * taxRate;
        const total = subtotal + taxDue;

        form.subtotal.value = subtotal.toFixed(2);
        form.tax_due.value = taxDue.toFixed(2);
        form.total.value = total.toFixed(2);
    });

    // Submit to API
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const data = new FormData(form);

        const res = await fetch('/api/invoice.php', {
            method: 'POST',
            body: data
        });

        const json = await res.json();
        alert(json.message || json.error);
    });
</script>
</body>
</html>
