<?php

namespace Jwohlfert23\LaravelAvalara;

use Illuminate\Support\Str;

enum AvalaraDocType: string
{
    case SALES_INVOICE = 'SalesInvoice';
    case SALES_ORDER = 'SalesOrder';

    case RETURN_INVOICE = 'ReturnInvoice';
    case RETURN_ORDER = 'ReturnOrder';

    case INVENTORY_TRANSFER_INVOICE = 'InventoryTransferInvoice';
    case INVENTORY_TRANSFER_ORDER = 'InventoryTransferOrder';

    case PURCHASE_INVOICE = 'PurchaseInvoice';
    case PURCHASE_ORDER = 'PurchaseOrder';

    public function isQuote(): bool
    {
        return Str::endsWith($this->value, 'Order');
    }
}
