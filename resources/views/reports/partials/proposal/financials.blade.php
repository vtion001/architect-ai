{{-- Proposal - Financials Section --}}
@if(!empty($financials) && !empty($financials['totalInvestment']))
    <h2>Strategic Investment & Capital Allocation</h2>
    <div class="callout">
        <p style="margin: 0; font-size: 1.1rem; font-weight: 700;">
            <strong>Infrastructure Value:</strong> 
            {{ number_format((float) $financials['totalInvestment'], 2) }} {{ $financials['currency'] ?? 'USD' }}
        </p>
        @if(!empty($financials['timeline']))
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
                <strong>Deployment Timeline:</strong> {{ $financials['timeline'] }}
            </p>
        @endif
        <p style="margin: 10px 0 0 0; font-size: 0.8rem; opacity: 0.8; font-style: italic;">
            * This investment reflects the reduction of long-term cognitive load and operational friction.
        </p>
    </div>

    @if(!empty($financials['paymentMilestones']))
        <h3>Infrastructure Investment Schedule</h3>
        <table>
            <thead>
                <tr>
                    <th>Phase / Milestone</th>
                    <th>Allocation</th>
                    <th>Investment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($financials['paymentMilestones'] as $milestone)
                    @if(!empty($milestone['name']) && !empty($milestone['percentage']))
                        <tr>
                            <td><strong>{{ $milestone['name'] }}</strong></td>
                            <td>{{ $milestone['percentage'] }}%</td>
                            <td>{{ number_format(((float)$milestone['percentage'] / 100) * (float)$financials['totalInvestment'], 2) }} {{ $financials['currency'] ?? 'USD' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
@endif