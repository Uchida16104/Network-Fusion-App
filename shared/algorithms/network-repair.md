# Network repair algorithm

## Objective

Identify structural defects in a network graph and generate a minimal repair plan.

## Method

1. Convert input nodes and edges to an undirected adjacency list.
2. Compute connected components with BFS.
3. Identify isolated nodes.
4. Use a focus node, if available, as the anchor component.
5. Bridge every disconnected component to the anchor.
6. Re-run connectivity verification after the proposed repair.

## Complexity

- Adjacency construction: `O(V + E)`
- Connected components: `O(V + E)`
- BFS shortest-path distances: `O(V + E)`

## Repair policy

The default plan favors structural connectivity over topology optimization. It is deterministic and easy to validate.
