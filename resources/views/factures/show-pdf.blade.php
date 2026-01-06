<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $facture->reference }}</title>

    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            /* Taille réduite pour gagner de l'espace */
            color: #000;
            line-height: 1.2;
        }

        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .client-info {
            width: 100%;
            margin-bottom: 15px;
        }

        /* Style compact sans bordures visibles sur les cellules */
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .product-table thead th {
            border-bottom: 1px solid #000;
            text-align: left;
            padding: 4px 2px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .product-table tbody td {
            padding: 3px 2px;
            border-bottom: 0.5px solid #eee;
            /* Ligne de séparation très légère */
        }

        /* Alignements et largeurs de colonnes */
        .col-ref {
            width: 15%;
            color: #666;
        }

        .col-name {
            width: 45%;
        }

        .col-qte {
            width: 10%;
            text-align: center;
        }

        .col-prix {
            width: 15%;
            text-align: right;
        }

        .col-total {
            width: 15%;
            text-align: right;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .total-box {
            margin-top: 15px;
            text-align: right;
            border-top: 1px double #000;
            padding-top: 5px;
        }

        .total-amount {
            font-size: 16px;
            font-weight: bold;
        }

        .signature {
            margin-top: 30px;
            width: 100%;
        }

        .signature-box {
            float: right;
            width: 180px;
            text-align: center;
        }

        .stamp-area {
            margin-top: 5px;
            height: 60px;
            border: 1px dashed #ccc;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="title">Bon de livraison</div>
                <strong>Réf : {{ $facture->reference }}</strong>
            </td>
            <td style="width: 50%; text-align: right; font-size: 10px; vertical-align: top;">
               
                <strong>SKY</strong><br>
                Tél : 0661642727 / 0661722999
            </td>
        </tr>
    </table>

    <table class="client-info">
        <tr>
            <td style="width: 60%;">
                <strong>Client :</strong> {{ $facture->client->client }}<br>
                <strong>Tél :</strong> {{ $facture->client->telephone ?? '-' }}
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <strong>Date :</strong> {{ $facture->created_at->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <table class="product-table">
        <thead>
            <tr>
                <th class="col-ref">Réf</th>
                <th class="col-name">Produit</th>
                <th class="col-qte">Qté</th>
                <th class="col-prix">Prix</th>
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($facture->items as $item)
            @php
            $prixTTC = $item->prix * 1.2;
            $ligne = $prixTTC * $item->quantite;
            $total += $ligne;
            @endphp
            <tr>
                <td class="col-ref">{{ $item->produit->reference ?? '-' }}</td>
                <td class="col-name">{{ $item->produit->name }}</td>
                <td class="col-qte">{{ $item->quantite }}</td>
                <td class="col-prix">{{ number_format($prixTTC, 2, ',', ' ') }}</td>
                <td class="col-total">{{ number_format($ligne, 2, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <span style="font-size: 12px; margin-right: 10px;">MONTANT À PAYER :</span>
        <span class="total-amount">{{ number_format($total, 2, ',', ' ') }} DH</span>
    </div>

    <div class="signature">
        <div class="signature-box">
            <strong>Signature</strong>
            <div class="stamp-area"></div>
        </div>
    </div>

</body>

</html>