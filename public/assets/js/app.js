$(document).ready(function () {
    $('.billing-toggle').click(function () {
        var selectedCycle = $(this).data('cycle');

        $('.billing-toggle').removeClass('active');
        $(this).addClass('active');

        $('.pricing-card').each(function () {
            var $card = $(this);
            var cardCycle = $card.data('cycle');
            var price, label;
            var currency = $card.data('currency');

            switch (selectedCycle) {
                case 'monthly':
                    price = $card.data('monthly');
                    label = $card.data('monthly-label');
                    break;
                case 'weekly':
                    price = $card.data('weekly');
                    label = $card.data('weekly-label');
                    break;
                case 'yearly':
                    price = $card.data('yearly');
                    label = $card.data('yearly-label');
                    break;
                case 'all':
                    // Show all cards using their original cycle
                    var cardDefaultCycle = $card.data('cycle');
                    price = (cardDefaultCycle === 'monthly') ? $card.data('monthly') :
                            (cardDefaultCycle === 'weekly') ? $card.data('weekly') :
                            (cardDefaultCycle === 'yearly') ? $card.data('yearly') :
                            $card.data('monthly'); // fallback
                    label = (cardDefaultCycle === 'monthly') ? $card.data('monthly-label') :
                            (cardDefaultCycle === 'weekly') ? $card.data('weekly-label') :
                            (cardDefaultCycle === 'yearly') ? $card.data('yearly-label') :
                            $card.data('monthly-label');
                    break;
            }

            $card.find('.price-display').fadeOut(100, function () {
                $(this).html(parseFloat(price).toFixed(2) + ' ' + currency + 
                    ' <small class="text-muted">' + label + '</small>').fadeIn(100);
            });

            // Show or hide cards
            if (selectedCycle === 'all' || selectedCycle === cardCycle) {
                $card.show();
            } else {
                $card.hide();
            }
        });
    });
});
