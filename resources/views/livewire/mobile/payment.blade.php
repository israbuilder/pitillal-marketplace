<div class="space-y-4 pb-24" x-data="stripePayment()" x-init="initialize()">
    <section class="rounded-3xl bg-slate-950 p-5 text-white">
        <p class="text-xs uppercase tracking-[.2em] text-slate-400">Pedido #{{ $order->id }}</p>
        <div class="mt-2 flex items-end justify-between">
            <h1 class="text-xl font-black">Pago con Stripe</h1>
            <p class="text-2xl font-black">${{ number_format($order->total, 2) }}</p>
        </div>
    </section>

    <section class="rounded-3xl bg-white p-5 shadow-sm">
        <div id="payment-element"></div>
        <p x-show="error" x-text="error" class="mt-3 rounded-2xl bg-rose-50 p-3 text-sm font-semibold text-rose-700"></p>
        <button @click="pay" :disabled="loading" class="mt-5 w-full rounded-2xl bg-indigo-600 py-4 font-black text-white disabled:opacity-50">
            <span x-show="!loading">Pagar ahora</span>
            <span x-show="loading">Procesando…</span>
        </button>
    </section>

    @script
    <script>
        Alpine.data('stripePayment', () => ({
            stripe: null,
            elements: null,
            loading: false,
            error: '',

            async initialize() {
                this.stripe = Stripe(@js($stripeKey));

                const response = await fetch(@js(route('mobile.payment.intent', $order)), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    this.error = data.message ?? 'No se pudo iniciar el pago.';
                    return;
                }

                this.elements = this.stripe.elements({
                    clientSecret: data.clientSecret,
                    appearance: {
                        theme: 'stripe',
                        variables: { borderRadius: '16px' }
                    }
                });

                this.elements.create('payment').mount('#payment-element');
            },

            async pay() {
                this.loading = true;
                this.error = '';

                const { error } = await this.stripe.confirmPayment({
                    elements: this.elements,
                    confirmParams: {
                        return_url: @js(route('mobile.orders.show', $order)),
                    },
                });

                if (error) {
                    this.error = error.message;
                    this.loading = false;
                }
            }
        }));
    </script>
    @endscript
</div>
