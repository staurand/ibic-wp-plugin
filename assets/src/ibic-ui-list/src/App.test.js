import { render, screen } from '@testing-library/react';
import ConnectedList from './App';

test('renders learn react link', () => {
  render(<ConnectedList />);
  const linkElement = screen.getByText(/learn react/i);
  expect(linkElement).toBeInTheDocument();
});
