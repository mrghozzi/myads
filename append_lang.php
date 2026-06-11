<?php
$en = file_get_contents('lang/en/messages.php');
$ar = file_get_contents('lang/ar/messages.php');

$en_append = "
    'pts_activities' => 'PTS Vouchers & Transfers',
    'pts_vouchers' => 'PTS Vouchers',
    'generator' => 'Generator',
    'code' => 'Code',
    'amount' => 'Amount',
    'status' => 'Status',
    'claimer' => 'Claimed By',
    'date' => 'Date',
    'used' => 'Used',
    'unused' => 'Unused',
    'no_records' => 'No records found',
    'pts_transfers' => 'PTS Transfers between members',
    'sender' => 'Sender',
    'recipient' => 'Recipient',
    'transfer_pts' => 'Transfer PTS',
    'username' => 'Username',
    'Points' => 'Points',
    'send' => 'Send',
    'generate_voucher' => 'Generate Voucher',
    'generate' => 'Generate',
    'claim_voucher' => 'Claim Voucher',
    'claim' => 'Claim',
    'cannot_transfer_to_self' => 'You cannot transfer points to yourself',
    'invalid_voucher_code' => 'Invalid or non-existent voucher code',
    'voucher_already_used' => 'Sorry, this voucher has already been used',
    'received_pts_transfer' => 'You received :amount PTS from :sender',
    'transfer_successful' => 'Successfully transferred :amount PTS to :recipient',
    'voucher_generated_success' => 'Voucher generated successfully',
    'voucher_claimed_by' => ':claimer claimed your voucher for :amount PTS',
    'voucher_claimed_success' => 'Successfully claimed :amount PTS',
];";

$ar_append = "
    'pts_activities' => 'نشاطات النقاط والبطاقات',
    'pts_vouchers' => 'بطاقات النقاط (القسائم)',
    'generator' => 'المولد',
    'code' => 'الكود',
    'amount' => 'الكمية',
    'status' => 'الحالة',
    'claimer' => 'المستخدم',
    'date' => 'التاريخ',
    'used' => 'مستخدمة',
    'unused' => 'غير مستخدمة',
    'no_records' => 'لا توجد سجلات',
    'pts_transfers' => 'تحويلات النقاط بين الأعضاء',
    'sender' => 'المرسل',
    'recipient' => 'المستلم',
    'transfer_pts' => 'إرسال نقاط PTS',
    'username' => 'اسم المستخدم',
    'Points' => 'النقاط',
    'send' => 'إرسال',
    'generate_voucher' => 'توليد بطاقة نقاط',
    'generate' => 'توليد',
    'claim_voucher' => 'استخدام بطاقة نقاط',
    'claim' => 'استخدام',
    'cannot_transfer_to_self' => 'لا يمكنك تحويل النقاط لنفسك',
    'invalid_voucher_code' => 'كود البطاقة غير صحيح أو غير موجود',
    'voucher_already_used' => 'عذراً، لقد تم استخدام هذه البطاقة مسبقاً',
    'received_pts_transfer' => 'لقد استلمت :amount PTS من العضو :sender',
    'transfer_successful' => 'تم تحويل :amount PTS بنجاح للعضو :recipient',
    'voucher_generated_success' => 'تم توليد بطاقة النقاط بنجاح',
    'voucher_claimed_by' => 'قام العضو :claimer باستخدام بطاقة النقاط الخاصة بك (:amount PTS)',
    'voucher_claimed_success' => 'تم إضافة :amount PTS إلى رصيدك بنجاح',
];";

$en = preg_replace('/\];\s*$/', $en_append, $en);
$ar = preg_replace('/\];\s*$/', $ar_append, $ar);

file_put_contents('lang/en/messages.php', $en);
file_put_contents('lang/ar/messages.php', $ar);
echo "Done.\n";
