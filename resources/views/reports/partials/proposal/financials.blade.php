{{-- Proposal - Financials Section --}}
@if(!empty($financials) && !empty($financials['totalInvestment']))
    <h2>Investment & Payment Structure</h2>
    <div class="callout">
        <p style="margin: 0; font-size: 1.1rem; font-weight: 700;">
            <strong>Total Investment:</strong> 
            {{ number_format((float) $financials['totalInvestment'], 2) }} {{ $financials['currency'] ?? 'USD' }}
        </p>
        @if(!empty($financials['timeline']))
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
                <strong>Estimated Project Timeline:</strong> {{ $financials['timeline'] }}
            </p>
        @endif
    </div>

    @if(!empty($financials['paymentMilestones']))
        <h3>Payment Schedule</h3>
        <table>
            <thead>
                <tr>
                    <th>Milestone</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($financials['paymentMilestones'] as $milestone)
                    @if(!empty($milestone['name']) && !empty($milestone['percentage']))
                        <tr>
                            <td>{{ $milestone['name'] }}</td>
                            <td>{{ $milestone['percentage'] }}%</td>
                            <td>{{ number_format(((float)$milestone['percentage'] / 100) * (float)$financials['totalInvestment'], 2) }} {{ $financials['currency'] ?? 'USD' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
@endif
