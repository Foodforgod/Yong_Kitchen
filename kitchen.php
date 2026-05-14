<?php
include 'db.php';

$pending_orders = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY id ASC");

function renderTableNumber($table_number) {
    if (preg_match('/^\s*Table\s+/i', $table_number)) {
        return htmlspecialchars($table_number);
    }
    return 'Table ' . htmlspecialchars($table_number);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Display | RMS</title>
    <meta http-equiv="refresh" content="120">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-card {
            border-radius: 22px;
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }
        .order-card-header {
            background: #111827;
            color: white;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .order-card-header span,
        .order-card-header small {
            display: block;
        }
        .order-table-number {
            font-size: 1rem;
            color: #bfdbfe;
            font-weight: 700;
        }
        .status-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.77rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.03em;
        }
        .status-pill.pending { background: rgba(251, 191, 36, 0.16); color: #b45309; }
        .status-pill.ready { background: rgba(16, 185, 129, 0.16); color: #047857; }

        .item-row {
            padding: 18px 22px;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .item-row:hover { background: #f8fafc; transform: translateY(-1px); }
        .item-row.done { background: #f1f5f9; opacity: 0.75; }
        .item-row.done .item-text { text-decoration: line-through; color: #94a3b8; }

        .item-text { display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .status-icon { margin-right: 10px; font-size: 1.2rem; transition: color 0.2s ease; }
        .done .status-icon { color: #16a34a; }

        .note-box {
            font-size: 0.9rem;
            color: #92400e;
            background: #fffbeb;
            padding: 10px 14px;
            border-radius: 10px;
            border-left: 4px solid #f59e0b;
            margin-top: 8px;
        }

        .btn-ready {
            width: 100%;
            padding: 15px;
            font-weight: bold;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, opacity 0.2s ease;
        }
        .btn-ready:hover:not(:disabled) { transform: translateY(-2px); }
        .btn-ready:disabled {
            background-color: #cbd5e1;
            color: #94a3b8;
            border: none;
            cursor: not-allowed;
            opacity: 0.65;
        }
        .order-footer {
            padding: 18px 22px 24px;
            background: #ffffff;
        }
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }
        .order-summary span { color: #475569; font-size: 0.95rem; }
    </style>
</head>
<body class="kitchen-body">

    <div class="kitchen-header">
        <h1><i class="fas fa-fire-alt"></i> KITCHEN QUEUE</h1>
        <div style="display:flex; align-items:center; gap:20px;">
            <span class="badge bg-pending"><?php echo $pending_orders->num_rows; ?> ORDERS</span>
            <a href="admin.php" class="btn btn-primary">Admin Panel</a>
        </div>
    </div>

    <div class="kitchen-grid">
        <?php if($pending_orders->num_rows > 0): ?>
            <?php while($o = $pending_orders->fetch_assoc()): ?>
            <div class="order-card" id="order-card-<?php echo $o['id']; ?>">
                <div class="order-card-header">
                    <span class="order-table-number"><?php echo renderTableNumber($o['table_number']); ?></span>
                    <small>Order #<?php echo $o['id']; ?></small>
                    <span class="status-pill pending"><?php echo strtoupper($o['status']); ?></span>
                </div>
                
                <div class="order-items-list" style="padding:0;">
                    <?php
                    $oid = $o['id'];
                    $items_query = $conn->query("SELECT oi.id, oi.quantity, oi.remarks, oi.item_status, i.name 
                                           FROM order_items oi 
                                           JOIN items i ON oi.item_id = i.id 
                                           WHERE oi.order_id = $oid");
                    
                    $items_list = [];
                    $total_items = 0;
                    $completed_count = 0;

                    while($row = $items_query->fetch_assoc()) {
                        $items_list[] = $row;
                        $total_items++;
                        if($row['item_status'] == 'done') $completed_count++;
                    }
                    
                    foreach($items_list as $i):
                        $is_done = ($i['item_status'] == 'done');
                    ?>
                        <div class="item-row <?php echo $is_done ? 'done' : ''; ?>"                              data-item-id="<?php echo $i['id']; ?>"                             data-order-id="<?php echo $o['id']; ?>"
                             onclick="toggleItem(<?php echo $i['id']; ?>, this)">
                            
                            <div class="item-text">
                                <i class="<?php echo $is_done ? 'fas fa-check-circle' : 'far fa-circle'; ?> status-icon"></i>
                                <b><?php echo $i['quantity']; ?>x</b> <?php echo htmlspecialchars($i['name']); ?>
                            </div>

                            <?php if(!empty($i['remarks'])): ?>
                                <div class="note-box">
                                    <i class="fas fa-sticky-note"></i> <?php echo htmlspecialchars($i['remarks']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-footer">
                    <div class="order-summary">
                        <span><strong><?php echo $total_items; ?></strong> item<?php echo $total_items !== 1 ? 's' : ''; ?></span>
                        <span><strong><?php echo $completed_count; ?></strong> ready</span>
                        <span><?php echo ($total_items - $completed_count); ?> remaining</span>
                    </div>
                </div>

                <div style="padding: 15px;">
                    <form action="update_status.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <button type="submit" name="mark_ready" 
                                id="ready-btn-<?php echo $o['id']; ?>"
                                class="btn-ready" 
                                <?php echo ($completed_count < $total_items) ? 'disabled' : ''; ?>>
                            <i class="fas fa-bell"></i> TABLE READY
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="grid-column: 1/-1; text-align:center; padding-top:100px;">
                <i class="fas fa-clipboard-check" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                <h2 style="color: #64748b;">No active orders right now.</h2>
            </div>
        <?php endif; ?>
    </div>

    <script>
    let lastUpdateTime = 0;
    let lastOrderIds = new Set();
    
    function renderTableNumber(tableNumber) {
        if (tableNumber.match(/^\s*Table\s+/i)) {
            return tableNumber;
        }
        return 'Table ' + tableNumber;
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;
        toast.innerHTML = `<i class="fas fa-bell"></i> ${message}`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function updateOrderSummary(orderId, totalItems, completedCount) {
        const card = document.getElementById('order-card-' + orderId);
        if (!card) return;
        
        const summary = card.querySelector('.order-summary');
        if (summary) {
            summary.innerHTML = `
                <span><strong>${totalItems}</strong> item${totalItems !== 1 ? 's' : ''}</span>
                <span><strong>${completedCount}</strong> ready</span>
                <span>${totalItems - completedCount} remaining</span>
            `;
        }
        
        const readyBtn = document.getElementById('ready-btn-' + orderId);
        if (readyBtn) {
            readyBtn.disabled = completedCount < totalItems;
        }
    }

    function updateItemRow(orderId, itemId, itemName, quantity, remarks, isDone) {
        const card = document.getElementById('order-card-' + orderId);
        if (!card) return;
        
        let itemRow = document.querySelector(`[data-item-id="${itemId}"]`);
        
        if (!itemRow) {
           
            const itemsList = card.querySelector('.order-items-list');
            const newRow = document.createElement('div');
            newRow.className = `item-row ${isDone ? 'done' : ''}`;
            newRow.setAttribute('data-item-id', itemId);
            newRow.setAttribute('data-order-id', orderId);
            newRow.onclick = function() { toggleItem(itemId, this); };
            
            const statusIcon = isDone ? 'fas fa-check-circle' : 'far fa-circle';
            let html = `
                <div class="item-text">
                    <i class="${statusIcon} status-icon"></i>
                    <b>${quantity}x</b> ${itemName}
                </div>
            `;
            
            if (remarks) {
                html += `<div class="note-box"><i class="fas fa-sticky-note"></i> ${remarks}</div>`;
            }
            
            newRow.innerHTML = html;
            itemsList.appendChild(newRow);
        } else {
            
            const wasDone = itemRow.classList.contains('done');
            if (isDone && !wasDone) {
                itemRow.classList.add('done');
                const icon = itemRow.querySelector('.status-icon');
                icon.className = 'fas fa-check-circle status-icon';
            } else if (!isDone && wasDone) {
                itemRow.classList.remove('done');
                const icon = itemRow.querySelector('.status-icon');
                icon.className = 'far fa-circle status-icon';
            }
        }
    }

    function pollForUpdates() {
        fetch('api/get_kitchen_orders.php')
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                
                const currentOrderIds = new Set();
                const ordersContainer = document.querySelector('.kitchen-grid');
                
                data.orders.forEach(order => {
                    currentOrderIds.add(order.id);
                    let card = document.getElementById('order-card-' + order.id);
                    
                    if (!lastOrderIds.has(order.id)) {
                        showToast(`🔔 New Order: ${renderTableNumber(order.table_number)}`, 'warning');
                    }
                    
                    order.items.forEach(item => {
                        const isDone = item.item_status === 'done';
                        updateItemRow(order.id, item.id, item.name, item.quantity, item.remarks, isDone);
                    });
                    
                    updateOrderSummary(order.id, order.total_items, order.completed_count);
                });
                
                document.querySelectorAll('[id^="order-card-"]').forEach(card => {
                    const orderId = card.id.replace('order-card-', '');
                    if (!currentOrderIds.has(parseInt(orderId))) {
                        card.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => card.remove(), 300);
                    }
                });
                
                if (data.orders.length === 0 && document.querySelectorAll('[id^="order-card-"]').length === 0) {
                    const emptyState = document.querySelector('.empty-state');
                    if (!emptyState) {
                        ordersContainer.innerHTML = `
                            <div class="empty-state" style="grid-column: 1/-1; text-align:center; padding-top:100px;">
                                <i class="fas fa-clipboard-check" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                                <h2 style="color: #64748b;">No active orders right now.</h2>
                            </div>
                        `;
                    }
                }
                
                lastUpdateTime = data.timestamp;
                lastOrderIds = currentOrderIds;
            })
            .catch(err => console.error('Error fetching updates:', err));
    }

    function toggleItem(itemId, element) {
        fetch('toggle_item_status.php?id=' + itemId)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const isDone = data.new_status === 'done';
                element.classList.toggle('done', isDone);
                
                const icon = element.querySelector('.status-icon');
                icon.className = isDone ? 'fas fa-check-circle status-icon' : 'far fa-circle status-icon';

                const orderId = element.getAttribute('data-order-id');
                const card = document.getElementById('order-card-' + orderId);
                const totalItems = card.querySelectorAll('.item-row').length;
                const doneItems = card.querySelectorAll('.item-row.done').length;
                const readyBtn = document.getElementById('ready-btn-' + orderId);

                if (totalItems === doneItems) {
                    readyBtn.disabled = false;
                } else {
                    readyBtn.disabled = true;
                }
                
                showToast('✓ Item marked complete', 'success');
            }
        })
        .catch(err => console.error('Error toggling status:', err));
    }

    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    setInterval(pollForUpdates, 5000);
    </script>
</body>
</html>