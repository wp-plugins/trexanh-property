<p><?php echo __( 'You need to do payment to complete your submission.', 'txp' ); ?></p>
<div><strong><?php echo __( 'Review Order', 'txp' ); ?></strong></div>
<table>
<tr>
<td style='width:30%;'><?php echo __( 'Property title', 'txp' ); ?></td>
<td><?php echo $post['post_title'];?></td>
</tr>
<tr>
<td><?php echo __( 'Amount', 'txp' ); ?></td>
<td><?php echo txp_currency($order_data['amount']); ?></td>
</tr>
<tr>
<td><?php echo __( 'Status', 'txp' ); ?></td>
<td><?php echo $order->post->post_status;?></td>
</tr>
</table>
<p>
<div><strong><?php echo __( 'Payment method', 'txp' ); ?></strong></div>
<div><input type='radio' checked><?php echo __( 'Paypal', 'txp' );?></div>
</p>
<form method='post' action='<?php echo site_url('/submit-property-payment/'); ?>'>
    <input type='hidden' name='post_id' value='<?php echo $post_id;?>' />
    <input type='submit' value='<?php echo __( 'Do payment', 'txp' ); ?>' />
</form>