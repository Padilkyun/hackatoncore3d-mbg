import networkx as nx

def calculate_fastest_route(bins_data, start_node="Base"):
    """
    bins_data: list of dicts with {id, name, lat, long, predicted_level}
    """
    G = nx.Graph()
    
    # Add Base/Depot
    G.add_node("Base", pos=(0, 0)) # Placeholder coordinates
    
    # Add Bins as nodes
    for b in bins_data:
        G.add_node(b['id'], pos=(b['lat'], b['long']), level=b['predicted_level'])
        
    # Connect nodes (for simplicity, connect everything to everything with Euclidean distance)
    nodes = list(G.nodes(data=True))
    for i in range(len(nodes)):
        for j in range(i + 1, len(nodes)):
            n1, d1 = nodes[i]
            n2, d2 = nodes[j]
            dist = ((d1['pos'][0] - d2['pos'][0])**2 + (d1['pos'][1] - d2['pos'][1])**2)**0.5
            G.add_edge(n1, n2, weight=dist)
            
    # Simple TSP-like heuristic or just shortest path to all bins > 80%
    critical_bins = [n for n, d in G.nodes(data=True) if d.get('level', 0) > 80]
    
    if not critical_bins:
        return ["No collection needed"]
        
    # Simplified: Find shortest path visiting all critical bins starting from Base
    # Using a simple greedy approach for demo purposes
    current = start_node
    route = [current]
    to_visit = critical_bins.copy()
    
    while to_visit:
        next_node = min(to_visit, key=lambda x: G[current][x]['weight'])
        route.append(next_node)
        to_visit.remove(next_node)
        current = next_node
        
    route.append(start_node) # Return to base
    return route
