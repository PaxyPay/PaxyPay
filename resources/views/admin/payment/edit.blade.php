@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row mt-3 mb-3 justify-content-center">
            <div class="col-12">
                <div class="card p-2 p-md-5">
                {{--  Modale --}}
                    <div id="overlay"></div>
                    <div class="modal" tabindex="-1" id="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Conferma eliminazione</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Sei sicuro di voler eliminare questo pagamento?
                            </div>
                            <div class="modal-footer">
                                <button onclick="closeModal()" type="button" id="closeModal" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Annulla</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Elimina</button>
                            </div>
                        </div>
                    </div>
                    </div>

                    <form action="{{ route('admin.payment.update', $payment->id) }}" method="POST" class=""enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="client_name"
                                value="{{ old('client_name', $payment->client_name) }}" name="client_name">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="4" value="" name="description">{{ old('description', $payment->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date"
                                value="{{ old('due_date', $payment->due_date) }}">
                        </div>
                        <div class="form-check form-switch">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Enabled</label>
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault"
                                value="{{ old('active', $payment->active) }}" {{ $payment->active == 1 ? 'checked' : '' }}
                                name="active">
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="p-3">Cart</h3>
                            </div>

                            <div class="d-flex flex-row-reverse">
                                <button type="button" id="addProductBtn" class="btn btn-success add">+ Add Product</button>
                            </div>
                        </div>
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="mt-4">
                            <div id="productSections" data-product-count="{{ count($payment->products) }}">
                                <!-- Qui verranno aggiunte dinamicamente le sezioni di aggiunta prodotti -->

                                @foreach ($payment->products as $i => $product)
                                    <div class="card px-md-3 my-3 pruduct-section">
                                        <div class="mb-3 pt-5">
                                            <button type="button" class="removeProductBtn delete-product btn btn-danger"><i class="fas fa-times-circle"></i></button>
                                            <div class="align-items-center row justify-content-between">
                                                <div class="col-md-4 justify-content-between d-md-block d-flex p-3">
                                                    <label for="product_name" class="form-label mx-2">Name</label>
                                                    <input type="text" class="form-control refresh name"
                                                        name="products[{{ $i }}][product_name]"
                                                        value="{{ old('product_name[]', $product->product_name) }}"
                                                        id="product_name">
                                                </div>
                                                <div class="col-md-4 justify-content-between d-md-block d-flex p-3">
                                                    <label for="quantity" class="form-label mx-2">Quantity</label>
                                                    <select type="number" class="form-control refresh quantity"
                                                        name="products[{{ $i }}][quantity]" id="quantity_select">
                                                        @for ($j = 1; $j <= 100; $j++)
                                                            <option value="{{ $j }}"
                                                                {{ $j == $product->quantity ? 'selected' : '' }}>
                                                                {{ $j }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="col-md-4 justify-content-between  d-flex flex-md-column p-3 align-items-end">
                                                    <label for="product_price" class="form-label mx-2">Price : &euro;</label>
                                                    <input type="number" class="form-control refresh price" step="0.01" min="1"
                                                        name="products[{{ $i }}][product_price]"
                                                        onchange="updateTotalPrice()"
                                                        value="{{ old('product_price[]', $product->product_price) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="cart_id[]" value="{{ $product->id }}">
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div class="card p-3 d-flex flex-row-reverse pruduct-section">
                            <div class="px-3">
                                <div class="d-flex justify-content-end flex-column align-items-end">
                                    <div class="d-flex no-wrap">
                                        <h4>Total Price : <span id="total_price"></span> &euro;</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5">
                            <a class="btn btn-secondary" href="{{ route('admin.payment.index') }}"><-back </a>
                            <button type="submit" id='submit' class="btn btn-primary mx-3">Edit</button>
                        </div>
                    </form>
                    <div class="col-6 col-md-3 my-3">
                        <form id="form_delete" class="form-delete" action="{{ route('admin.payment.destroy', $payment->id) }}" method="POST" data-delete-name="{{ $payment->client_name }}">
                        @csrf
                        @method('DELETE')
                            <div>
                            <a id="button_delete" class="btn btn-danger w-100 form-delete-btn">Delete</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
           
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            updateTotalPrice()
            let sectionCount = document.getElementById('productSections');
            let index = parseInt(sectionCount.getAttribute('data-product-count')) || 0;


            document.getElementById("addProductBtn").addEventListener("click", function() {
                // Verifica se tutti i campi del prodotto precedente sono stati compilati
                index++;
                var lastProductSection = document.querySelector("#productSections .card:last-of-type");
                if (lastProductSection) {
                    var inputs = lastProductSection.querySelectorAll("input");
                    var isFilled = true;
                    inputs.forEach(function(input) {
                        if (input.value.trim() === "") {
                            isFilled = false;
                            return;
                        }
                    });

                    // Se i campi del prodotto precedente non sono stati compilati, mostra un messaggio di errore
                    if (!isFilled) {
                        return
                    }
                }

                // Crea una nuova sezione di aggiunta prodotto
                var productSection = document.createElement("div");


                // Aggiungi gli input per il nuovo prodotto
                productSection.innerHTML =

                    `
                <div class="card px-md-3 my-3 pruduct-section">
                    <div class="mb-3 pt-5">
                        <button type="button" class="removeProductBtn delete-product btn btn-danger"><i class="fas fa-times-circle"></i></button>
                            <div class="align-items-center row justify-content-between">
                                <div class="col-md-4 justify-content-between d-md-block d-flex p-3">
                                    <label for="product_name" class="form-label  mx-2">Name</label>
                                    <input type="text" class="form-control refresh"
                                        name="products[${index}][product_name]"
                                        value="{{ old('products.${index}.product_name') }}">
                                </div>
                                <div class="col-md-4 justify-content-between d-md-block d-flex p-3">
                                    <label for="quantity" class="form-label  mx-2">Quantity</label>
                                    <select type="number" class="form-control refresh quantity" name="products[${index}][quantity]">
                                        @for ($j = 1; $j <= 100; $j++)
                                        <option value="{{ $j }}" >{{ $j }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-4 justify-content-between  d-flex flex-md-column p-3 align-items-end">
                                    <label for="product_price" class="form-label mx-2">Price : &euro;</label>
                                    <input type="number" class="form-control refresh price" step="0.01"
                                        name="products[${index}][product_price]" onchange="updateTotalPrice()"
                                        value="{{ old('products.${index}.product_price') }}">
                                </div>
                            </div>`;

                // Aggiungi la nuova sezione di aggiunta prodotto al DOM
                document.getElementById("productSections").appendChild(productSection);
                productSection.querySelectorAll('.refresh').forEach(function(element) {
                    element.addEventListener('input', updateTotalPrice);
                });
            });

            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("removeProductBtn")) {
                    event.target.closest(".card").remove(); // Rimuovi la sezione del prodotto più vicina
                    updateTotalPrice();
                }
            });



            function updateTotalPrice() {
                var productPrices = document.getElementsByClassName("price") || 0;
                var quantities = document.getElementsByClassName("quantity");
                var totalPrice = 0;
                for (let p = 0; p < productPrices.length; p++) {

                    var price = parseFloat(productPrices[p].value);
                    var quantity = parseFloat(quantities[p].value);
                    totalPrice += price * quantity;
                }

                if (isNaN(totalPrice)) {
                    totalPrice = totalPrice

                } else {
                    document.getElementById("total_price").innerHTML = totalPrice.toFixed(2);
                }
                console.log(totalPrice)
                if (totalPrice === 0) {
                    document.getElementById("total_price").innerHTML = 0 .toFixed(2);
                }
            }

            let buttonDelete = document.getElementById('button_delete');
            let modal = document.getElementById('modal');
            let overlay = document.getElementById('overlay');
            let form = document.getElementById('form_delete');

            buttonDelete.addEventListener('click', function(e) {
                e.preventDefault();
                overlay.style.display = 'block';
                modal.style.display = 'block';
            })

            let modalClose = document.getElementById('confirmDeleteBtn');
            modalClose.addEventListener('click', function() {
                form.submit();
            })

            function closeModal() {
                overlay.style.display = 'none';
                modal.style.display = 'none';

            }

            // Aggiungi un listener per l'evento "input" su tutti gli input e textarea
            document.querySelectorAll('input, textarea').forEach(function(element) {
                element.addEventListener('input', updateTotalPrice);
            });

            // Aggiungi un listener per l'evento "keypress" su tutti gli input di tipo text
            document.querySelectorAll('input[type="text"]').forEach(function(element) {
                element.addEventListener('keypress', updateTotalPrice);
            });

            // Aggiungi un listener per l'evento "click" su tutti i bottoni
            document.querySelectorAll('button').forEach(function(element) {
                element.addEventListener('click', updateTotalPrice);
            });

            $(document).ready(function() {
                $('#quantity_select').change(function() {
                    updateTotalPrice();
                });
            });
        })
    </script>

@endsection
